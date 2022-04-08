<?php

namespace App\Clients\Middlewares;

use App\Entities\OrderInTransit;
use App\Exceptions\OrderMiddlewareException;
use App\Locale\PoshubLocale;
use App\Models\OrderInError;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Class AbstractOrderMiddleware
 *
 * Order middlewares represent the step needed to process an order.
 * Each of them needs to extend this abstract class and define the $stepNumber and $stepName variable.
 *
 * @package App\Clients\Middlewares
 */
abstract class AbstractOrderMiddleware
{
    protected ?int $stepNumber = null;

    protected ?string $stepName = null;

    protected ?string $logChannel = null;

    protected array $errors = [];

    protected PoshubLocale $poshubLocale;

    /**
     * AbstractOrderMiddleware constructor.
     * @throws OrderMiddlewareException
     */
    public function __construct()
    {
        if (is_null($this->stepNumber)) {
            throw new OrderMiddlewareException(self::class . " - step number is not defined");
        }
        if (is_null($this->stepName)) {
            throw new OrderMiddlewareException(self::class . " - step name is not defined");
        }
        if (is_null($this->logChannel)) {
            throw new OrderMiddlewareException(self::class . " - log channel is not defined");
        }

        $this->poshubLocale = new PoshubLocale();
    }

    public function getStep(): string
    {
        return 'Step ' . $this->stepNumber . ' - ' . $this->stepName;
    }

    /**
     * Process the order in transit, return an order in transit.
     *
     * @param  OrderInTransit           $orderInTransit
     * @return OrderInTransit
     * @throws OrderMiddlewareException
     */
    abstract public function process(OrderInTransit $orderInTransit): OrderInTransit;

    /**
     * @param  OrderInTransit            $orderInTransit
     * @param  AbstractOrderMiddleware[] $stack
     * @return OrderInTransit
     * @throws OrderMiddlewareException
     */
    public function processAndExecuteNext(OrderInTransit $orderInTransit, array $stack): OrderInTransit
    {
        Log::channel($this->logChannel)->info($this->getStep() . ': Started');
        $orderInTransit = $this->process($orderInTransit);
        $orderInTransit->addMessage("Step " . $this->stepNumber . " - OK");
        Log::channel($this->logChannel)->info($this->getStep() . ': Completed');

        if (empty($stack)) {
            return $orderInTransit;
        }
        $next = array_shift($stack);
        return $next->processAndExecuteNext($orderInTransit, $stack);
    }

    /**
     * @param  OrderInTransit           $orderInTransit
     * @param  array                    $constrains
     * @throws OrderMiddlewareException
     */
    protected function validateOrder(OrderInTransit $orderInTransit, array $constrains): void
    {
        $validator = Validator::make($orderInTransit->raw, $constrains);

        if ($validator->fails()) {
            $message = $this->getStep() . ': FAILED';
            $inError = $this->createOrderInError(
                $message,
                serialize($validator->errors()->messages()),
                $orderInTransit
            );
            $message .= ", created OrderInError with id " . $inError->id;

            Log::channel($this->logChannel)->error($message);
            throw new OrderMiddlewareException($message);
        }
    }

    protected function createOrderInError(
        string $message,
        string $errors,
        OrderInTransit $orderInTransit
    ) : OrderInError
    {
        return OrderInError::create([
            'message' => $message,
            'errors' => $errors,
            'order_in_transit' => $orderInTransit->serialize(),
            'created_by_id' => 1,
            'updated_by_id' => 1,
        ]);
    }

    /**
     * Log the error and create an order in error
     *
     * @param  \Throwable     $exception
     * @param  OrderInTransit $orderInTransit
     * @return string
     */
    protected function createOrderInErrorAndLogThrowable(\Throwable $exception, OrderInTransit $orderInTransit): string
    {
        $message = $this->getStep() . ': UNEXPECTED ERROR';
        $inError = $this->createOrderInError(
            $message,
            $exception->getCode() . " - " . $exception->getMessage(),
            $orderInTransit
        );
        $message .=  ", created OrderInError with id " . $inError->id;

        $this->logThrowable($exception, $message);

        return $message;
    }

    protected function logThrowable(\Throwable $exception, string $message)
    {
        Log::channel($this->logChannel)->info($exception->getCode() . " - " . $exception->getMessage());
        Log::channel($this->logChannel)->info($exception->getTraceAsString());
        Log::channel($this->logChannel)->error($message);
    }
}
