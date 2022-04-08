<template>
    <v-card>
        <v-card-title>
            {{ $t('orders_accepted.title') }}
            <v-spacer></v-spacer>
            <v-col cols="2" class="py-0">
                <v-select
                    v-model="sortBy"
                    label="Sort By"
                    :items="[{text:'Date', value:'created_at'}]"
                    @change="()=>{fetchList(1)}"
                    :disabled="dataList === ''"
                ></v-select>
            </v-col>
            <v-col cols="2" class="py-0">
                <v-select
                    v-model="sortByType"
                    label="Type"
                    :items="['desc', 'asc']"
                    @change="()=>{fetchList(1)}"
                    :disabled="dataList === ''"
                ></v-select>
            </v-col>
        </v-card-title>
        <v-data-table
            :headers="headers"
            :items="dataList === ''? []: dataList"
            sort-by="id"
            class="elevation-1"
            :loading="dataList === ''"
            loading-text="Loading... Please wait"
            hide-default-footer
            :items-per-page="itemsPerPage"
        >
            <template v-slot:top>
                <v-container>
                    <v-row>
                        <v-col><div class="mt-1"><b>{{ $t('orders_accepted.filters') | uppercase }}:</b></div></v-col>
                        <v-col cols="2">
                            <v-menu
                                :close-on-content-click="true"
                                transition="scale-transition"
                                offset-y
                                min-width="auto"
                                :disabled="dataList === ''"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        v-model="filters.date"
                                        :label="$t('orders_accepted.date') | capitalize"
                                        prepend-icon="mdi-calendar"
                                        readonly
                                        v-bind="attrs"
                                        v-on="on"
                                        dense
                                        @input="()=>{fetchList(1)}"
                                        :disabled="dataList === ''"
                                    ></v-text-field>
                                </template>
                                <v-date-picker
                                    v-model="filters.date"
                                    no-title
                                    scrollable
                                    @input="()=>{fetchList(1)}"
                                >
                                    <!--<v-spacer></v-spacer>
                                    <v-btn
                                        text
                                        color="primary"
                                        @click="filters.datePicker = false"
                                    >
                                        {{ $t('common.cancel') }}
                                    </v-btn>
                                    <v-btn
                                        text
                                        color="primary"
                                        @click="$refs.datePicker.save(filters.date)"
                                    >
                                        {{ $t('common.ok') }}
                                    </v-btn>-->
                                </v-date-picker>
                            </v-menu>
                        </v-col>
                        <v-col class="text-center">
                            <v-select
                                :items="statusItems === ''?[]:statusItems"
                                item-text="name"
                                item-value="value"
                                :label="$t('orders_accepted.status') | capitalize"
                                dense
                                v-model="filters.status"
                                :disabled="dataList === ''"
                                @change="()=>{fetchList(1)}"
                            ></v-select>
                        </v-col>

                        <v-col class="text-center">
                            <v-select
                                :items="sourceItems===''?[]:sourceItems"
                                item-text="name"
                                item-value="value"
                                :label="$t('orders_accepted.source') | capitalize"
                                dense
                                v-model="filters.source"
                                :loading="sourceItems === ''"
                                :disabled="dataList === ''"
                                @change="()=>{fetchList(1)}"
                            ></v-select>
                        </v-col>

                        <v-col class="text-center">
                            <v-select
                                :items="paymentItems===''?[]:paymentItems"
                                item-text="name"
                                item-value="value"
                                :label="$t('orders_accepted.payment') | capitalize"
                                dense
                                v-model="filters.payment"
                                :loading="paymentItems === ''"
                                :disabled="dataList === ''"
                                @change="()=>{fetchList(1)}"
                            ></v-select>
                        </v-col>

                        <v-col class="text-center">
                            <v-select
                                :items="typeItems===''?[]:typeItems"
                                item-text="name"
                                item-value="value"
                                :label="$t('orders_accepted.type') | capitalize"
                                dense
                                v-model="filters.type"
                                :loading="typeItems === ''"
                                :disabled="dataList === ''"
                                @change="()=>{fetchList(1)}"
                            ></v-select>
                        </v-col>

                        <v-col class="text-center">
                            <v-select
                                :items="posItems===''?[]:posItems"
                                item-text="name"
                                item-value="value"
                                label="Pos Sync"
                                dense
                                v-model="filters.isPos"
                                :disabled="dataList === ''"
                                @change="()=>{fetchList(1)}"
                            ></v-select>
                        </v-col>

                        <v-col class="text-center pt-0" cols="2">
                            <v-switch
                                v-model="autoRefresh"
                                dense
                                :label="`Auto Refresh in ${intervalTime-counter} sec`"
                                :disabled="dataList === ''"
                            ></v-switch>
                        </v-col>
                        <v-col class="text-center">
                            <v-select
                                v-model="intervalTime"
                                label="Interval Time"
                                dense
                                :items="[10,30,90,300]"
                                :disabled="dataList === ''"
                                @change="reset"
                            ></v-select>
                        </v-col>

                        <!--<v-col class="text-center">
                            <v-select :items="shopItems"
                                      item-text="name"
                                      item-value="value"
                                      :label="$t('orders_accepted.shop') | capitalize"
                                      dense
                                      v-model="filters.shop"
                                      :loading="shopItems === ''"
                            ></v-select>
                        </v-col>-->
                    </v-row>
                </v-container>
            </template>

            <template #item.time="{ item }">{{ getValueTime(item) }}</template>

            <template #item.actions="{ item }">
                <v-btn icon class="text-center" :to="{ name: 'dashboard-orders-details', params: { order: item }, query:{id:item.id}}">
                    <v-icon>mdi-open-in-new</v-icon>
                </v-btn>
            </template>

            <template v-slot:no-data>
                <v-btn color="primary" @click="reset">Reset</v-btn>
            </template>
        </v-data-table>
        <v-card
            class="d-flex justify-space-between pa-4"
            style="gap: 10rem;"
        >
            <v-col cols="2" class="py-0">
                <v-text-field
                    v-model.number="page"
                    label="jump to"
                    type="number"
                    min="1"
                    :max="pageCount"
                    @change="fetchList"
                ></v-text-field>
            </v-col>
            <v-pagination
                v-model="page"
                :length="pageCount"
                @input="fetchList"
                :disabled="dataList === ''"
                :total-visible="9"
            ></v-pagination>
            <v-col cols="2" class="py-0">
                <v-select
                    v-model="itemsPerPage"
                    label="Items per page"
                    :items="[5,10,30,50]"
                    @change="()=>{fetchList(1)}"
                ></v-select>
            </v-col>
        </v-card>
    </v-card>
</template>

<script>
    import mixin from "../../../helpers/mixin"
    import { mapState } from "vuex"

    export default {
        mixins: [mixin],
        data: () => ({
            page: 1,
            pageCount: 0,
            itemsPerPage: 10,
            name: null,
            search: '',
            sortBy: 'created_at',
            sortByType: 'desc',
            sortList:[
                {text:'Date', value:'created_at'},
                {text:'Name', value:'name'},
            ],
            intervalId: null,
            intervalTime:30,
            counter:0,
            autoRefresh: true,
            filters: {
                status: '',
                source: '',
                payment: '',
                type: '',
                isPos:'',
                date: new Date().toISOString().substr(0, 10),
            },
            statusItems: [{ name: 'All', value: '' }],
            sourceItems: [{ name: 'All', value: '' }],
            paymentItems: [{ name: 'All', value: '' }],
            typeItems: [{ name: 'All', value: '' }],
            shopItems: [{ name: 'All', value: '' }],
            posItems: [{ name: 'All', value: '' },{name:'Sync', value:"1"},{name:'Not Sync', value:"0"},],
            dataList: '',
        }),

        computed: {
            ...mapState('app', ['shop']),
            headers () {
                return [
                    {
                        text: this.$t('orders_accepted.st'),
                        value: 'order_status.name' || 'order_status.code' ,
                    },
                    {
                        text: '#',
                        value: 'order_number',
                    },
                    {
                        text: _.capitalize(this.$t('orders_accepted.date')),
                        value: 'order_date',
                        filter: (value) => {
                            if (!this.filters.date) return true
                            return value === this.filters.date
                        },
                        align: ' d-none'
                    },
                    {
                        text: _.capitalize(this.$t('orders_accepted.received')),
                        value: 'order_time'
                    },
                    {
                        text: _.capitalize(this.$t('orders_accepted.name')),
                        value: 'customer.name'
                    },
                    {
                        text: _.capitalize(this.$t('orders_accepted.address')),
                        value: 'address.street'
                    },
                    {
                        text: _.capitalize(this.$t('orders_accepted.postcode')),
                        value: 'address.postcode'
                    },
                    {
                        text: _.capitalize(this.$t('orders_accepted.city')),
                        value: 'address.city'
                    },
                    {
                        text: _.capitalize(this.$t('orders_accepted.phone')),
                        value: 'customer.phone'
                    },
                    {
                        text: _.capitalize(this.$t('orders_accepted.amount')),
                        value: 'total_price'
                    },
                    {
                        text: _.capitalize(this.$t('orders_accepted.source')),
                        value: 'order_source.order_source_type.name' || "order_source.order_source_type.code",
                    },
                    {
                        text: _.capitalize(this.$t('orders_accepted.payment')),
                        value: 'payment_method.name' || 'payment_method.code',
                    },
                    {
                        text: _.capitalize(this.$t('orders_accepted.type')),
                        value: 'order_type.name' || 'order_type.code',
                    },
                    {
                        text: _.capitalize(this.$t('orders_accepted.time')),
                        value: 'time'
                    },
                    {
                        text: _.capitalize(this.$t('orders_accepted.shop')),
                        value: 'shop.name',
                    },
                    {
                        text: 'Is Pos Sync',
                        value: 'is_pos_sync',
                    },
                    {
                        text: _.capitalize(this.$t('orders_accepted.actions')),
                        align: 'end',
                        value: 'actions',
                        sortable: false
                    }
                ]
            }
        },

        watch: {
            shop(newVal, oldVal) {
                if (newVal !== oldVal) {
                    this.fetchList();
                }
            }
        },

        created () {
            if (!!this.shop) {
                this.fetchList();
            }
            this.fetchOrderStatusList();
            this.fetchSourceTypeList();
            this.fetchPaymentMethodList();
            this.fetchOrderTypeList();
        },

        beforeDestroy(){
            clearInterval(this.intervalId);
        },

        methods: {
            autoRefreshCheck: function (){
                let self = this;
                if(this.autoRefresh) {
                    this.autoRefresh = false;
                    this.dataList = '';
                    const params = {
                        key: 'qazplm@123'
                    };
                    const queryString = new URLSearchParams(params).toString();
                    axios.get(`/web/v1/orders/pos/pull?${queryString}`)
                        .then(function (resp) {
                            console.log(resp);
                            self.reset();
                        })
                        .catch(function (err) {
                            console.log(err);
                        });
                }
            },
            reset: function(){
                this.per_page = 10;
                this.page = 1;
                this.sortBy = 'created_at';
                this.sortByType = 'desc';
                this.filters = {
                    status: '',
                    source: '',
                    payment: '',
                    type: '',
                    isPos: '',
                    date: new Date().toISOString().substr(0, 10),
                }
                /* if (!!this.intervalId) {
                     clearInterval(this.intervalId);
                 }*/
                this.counter = 0;
                this.autoRefresh= true;
                this.fetchList();
            },
            fetchList (page = this.page) {
                let self = this;
                /*if (!!this.intervalId) {
                    clearInterval(this.intervalId);
                }*/
                this.dataList = '';
                this.page = page;
                const params = {
                    ...this.filters,
                    per_page: this.itemsPerPage,
                    page: this.page,
                    sortBy: this.sortBy,
                    sortByType: this.sortByType,
                }
                const queryString = new URLSearchParams(params).toString();
                axios.get(`/web/v1/orders/active/shop/${this.shop.id}?${queryString}`)
                    .then(function (response) {
                        console.log(response);
                        if(self.intervalId === null) {
                            self.intervalId = setInterval(() => {
                                    if (self.autoRefresh) {
                                        if (self.counter === self.intervalTime) {
                                            self.autoRefreshCheck();
                                        } else {
                                            self.counter++
                                        }
                                    } else {
                                        self.counter = 0;
                                        // clearInterval(self.intervalId);
                                    }
                                },
                                1000);
                        }
                        self.dataList = response.data.data;
                        self.pageCount = response.data.meta.last_page;
                    })
                    .catch(function (resp) {
                        console.log(resp);
                    });
            },
            fetchOrderStatusList: function () {
                const _this = this;
                axios.get('/web/v1/order-statuses')
                    .then(function (response) {
                        console.log(response);
                        // _this.statusItems.push({ name: _this.$t('common.all'), value: '' });
                        _.map(response.data.data, (item)=>{
                            if(item.parent_id === null) {
                                _this.statusItems.push({name: item.name, value: item.id});
                            }
                        });
                    })
                    .catch(function (resp) {
                        console.log(resp);
                    });
            },
            fetchSourceTypeList: function () {
                const _this = this;
                axios.get('/web/v1/order-source-types')
                    .then(function (response) {
                        console.log(response);
                        // _this.sourceItems.push({ name: _this.$t('common.all'), value: '' });
                        _.map(response.data.data, (item)=>{
                            _this.sourceItems.push({name: item.name, value: item.id});
                        });
                    })
                    .catch(function (resp) {
                        console.log(resp);
                    });
            },
            fetchPaymentMethodList: function () {
                const _this = this;
                axios.get('/web/v1/payment-methods')
                    .then(function (response) {
                        console.log(response);
                        // _this.paymentItems.push({ name: _this.$t('common.all'), value: '' });
                        _.map(response.data.data, (item)=>{
                            if(item.parent_id === null) {
                                _this.paymentItems.push({name: item.name, value: item.id});
                            }
                        });
                    })
                    .catch(function (resp) {
                        console.log(resp);
                    });
            },

            fetchOrderTypeList: function () {
                const _this = this;
                axios.get('/web/v1/order-types')
                    .then(function (response) {
                        console.log(response);
                        // _this.typeItems.push({ name: _this.$t('common.all'), value: '' });
                        _.map(response.data.data, (item)=>{
                            if(item.parent_id === null) {
                                _this.typeItems.push({name: item.name, value: item.id});
                            }
                        });
                    })
                    .catch(function (resp) {
                        console.log(resp);
                    });
            },

        }
    }
</script>
