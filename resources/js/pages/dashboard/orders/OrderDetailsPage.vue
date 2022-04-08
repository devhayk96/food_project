<template>
    <div class="d-flex flex-grow-1 flex-column">
        <v-container style="height: 400px;" v-if="!dataDetails">
            <v-row
                class="fill-height"
                align-content="center"
                justify="center"
            >
                <v-col
                    class="subtitle-1 text-center"
                    cols="12"
                >
                    Fetching data...
                </v-col>
                <v-col cols="6">
                    <v-progress-linear
                        color="primary accent-4"
                        indeterminate
                        rounded
                        height="6"
                    ></v-progress-linear>
                </v-col>
            </v-row>
        </v-container>
        <v-row class="flex-grow-0" dense v-else>
            <v-col cols="12">
                <v-card class="pt-3 pa-2">
                    <v-btn
                        class="mx-3"
                        dark
                        outlined
                        color="primary"
                        @click="$router.go(-1)"
                    >
                        <v-icon small dark>
                            mdi-arrow-left
                        </v-icon>
                        &nbsp;
                        Go Back
                    </v-btn>
                    <v-card-title class="mx-sm-5 mt-4 mb-5 pa-2 pa-sm-4 card-title-style">

                        <img v-show="dataDetails.order_type.code === 'delivery'" src="/images/custom/delivery-bike.svg" alt="delivery bike" class="mr-1" width="40"/>
                        <img v-show="dataDetails.order_type.code === 'pickup'" src="/images/custom/take-away.svg" alt="take away" class="mr-1" width="40"/>
                        <img v-show="dataDetails.order_type.code === 'dine_in'" src="/images/custom/dine-in.svg" alt="dine in logo" class="mr-1" width="40"/>
                        <h2 v-show="dataDetails.daily_order_number"> #{{ dataDetails.daily_order_number}}</h2>

                        <v-spacer></v-spacer>
                        <v-dialog scrollable v-model="dialogView" min-width="300px" width="50%">
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn class="text-uppercase"
                                    color="grey darken-4"
                                    outlined
                                    large
                                    v-bind="attrs"
                                    v-on="on"
                                > {{ $t('orders_details.log')}} </v-btn>
                                <v-divider class="mx-2 mx-sm-4" vertical></v-divider>
                                <img v-show="dataDetails.order_source.order_source_type.code === 'thuisbezorgd'" src="/images/custom/thuisbezorgd.png" alt="Thuisbezorgd" width="45" class="rounded-circle"/>
                                <img v-show="dataDetails.order_source.order_source_type.code === 'pos'" src="/images/custom/pos.png" alt="POS" width="45" class="rounded-circle"/>
                                <img v-show="dataDetails.order_source.order_source_type.code === 'ubereats'" src="/images/custom/uber-eats.png" alt="Uber Eats" width="45" class="rounded-circle"/>
                                <img v-show="dataDetails.order_source.order_source_type.code === 'kiosk'" src="/images/custom/kiosk.png" alt="Kiosk" width="45" class="rounded-circle"/>
                            </template>
                            <v-card>
                                <v-card-title>
                                    <span class="headline">{{ $t('orders_details.original_json') }}</span>
                                </v-card-title>

                                <v-card-text>
                                    <v-container>
                                        <pre>{{ JSON.parse(dataDetails.order_json) }}</pre>
                                    </v-container>
                                </v-card-text>

                                <v-card-actions>
                                    <v-spacer></v-spacer>
                                    <v-btn color="blue darken-1" text @click="dialogView = false">Close</v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-dialog>
                    </v-card-title>
                    <v-card-text class="pt-3 pt-sm-0">
                        <v-card elevation="0" color="white lighten-4 rounded-0" class="pa-sm-5">
                            <!--                            <h3 class="font-weight-bold text-center">{{ dataDetails.shop.name || '-' }}</h3><br>-->

                            <v-row>
                                <v-col class="col-lg-12 col-12 white table-style">
                                    <v-row>
                                        <v-col  class="col-12 pb-0" v-if="dataDetails.order_status.code === 'new'">
                                            <v-container style="" v-if="status !== ''">
                                                <v-row
                                                    class="fill-height"
                                                    align-content="center"
                                                    justify="center"
                                                >
                                                    <!--<v-col
                                                        class="subtitle-1 text-center"
                                                        cols="12"
                                                    >
                                                        Updating data...
                                                    </v-col>-->
                                                    <v-col cols="6">
                                                        <v-progress-linear
                                                            color="primary accent-4"
                                                            indeterminate
                                                            rounded
                                                            height="6"
                                                        ></v-progress-linear>
                                                    </v-col>
                                                </v-row>
                                            </v-container>
                                            <div class="text-center" v-else-if="userCan('orders-update')">
                                                <v-btn class="mx-2" dark color="success" @click="()=>{
                                        status = 'accept';
                                        updateStatus();
                                    }" :disabled="status!== ''">
                                                    Accept
                                                </v-btn>
                                                <v-btn class="mx-2" fab dark x-small color="primary" @click="()=>{
                                        newDateTime = $moment(newDateTime).add(-5, 'minutes')
                                    }" :disabled="status!== ''">
                                                    <v-icon dark>mdi-minus</v-icon>
                                                </v-btn>
                                                <b class="black--text">{{getTime(newDateTime)}}</b>
                                                <v-btn class="mx-2" fab dark x-small color="primary"  @click="()=>{
                                        newDateTime = $moment(newDateTime).add(5, 'minutes')
                                    }" :disabled="status!== ''">
                                                    <v-icon dark>mdi-plus</v-icon>
                                                </v-btn>
                                                <v-btn class="mx-2" dark color="error" @click="()=>{
                                        status = 'decline';
                                        updateStatus();
                                    }" :disabled="status!== ''">
                                                    Deny
                                                </v-btn>
                                            </div>
                                        </v-col>
                                        <v-col class="col-12 pt-0">
                                            <SimpleTableCard
                                                :data="_.map(dataDetails.order_products, (o)=>{
                                            return {
                                                'product': o.product_details.name,
                                                'image': o.product_details.image,
                                                'art_#': o.product_details.article_number,
                                                'quantity': o.quantity,
                                                'price': parseFloat(o.unit_price).toFixed(2),
                                                'total_price': parseFloat(o.total_price).toFixed(2),
                                                'remarks': o.remarks,
                                            }
                                        })"
                                                :label="$t('orders_details.order_details')"
                                                :shopname="dataDetails.shop.name || $t('orders_details.order_details')"
                                                :sourcename="dataDetails.order_source.order_source_type.name + ' ' + $t('orders_details.order_id')"
                                                :orderid="JSON.parse(dataDetails.order_json).publicReference || dataDetails.order_number"
                                                :subtotal="parseFloat(dataDetails.sub_total).toFixed(2)"
                                                :elevation="'elevation-0'"
                                                :language="'orders_details'"
                                                :exclude="['category', 'note']"
                                                :merge="{ product: 'image' }"
                                                :subtext="{ product: 'remarks' }"
                                                :headers-props="[
                                                    {
                                                        text: 'quantity',
                                                        value: 'quantity',
                                                        align: 'start'
                                                     },
                                                     {
                                                        text: 'product',
                                                        value: 'product',
                                                        align: 'start'
                                                     },
                                                     {
                                                        text: 'art_#',
                                                        value: 'art_#',
                                                        align: 'start'
                                                     },
                                                     {
                                                        text: 'price',
                                                        value: 'price',
                                                        align: 'right'
                                                     },
                                                     {
                                                        text: 'total_price',
                                                        value: 'total_price',
                                                        align: 'right'
                                                     },
                                                ]"
                                            >
                                            </SimpleTableCard>
                                        </v-col>
                                    </v-row>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col class="col-lg-7 col-12 card-table-style">
                                <v-card elevation="0">
                                     <v-card-title>
                                        <img src="/images/custom/paid_black.svg" alt="Extra costs and discount" class="mr-1" width="40"/>
                                        <h2>Extra costs and discount</h2>
                                    </v-card-title>
                                </v-card>
                                    <v-card class="mb-2">
                                        <v-card-text>
                                            <v-row class="ma-0">
                                                <span>{{ $t('orders_details.delivery_cost') | capitalize }}</span>
                                                <v-spacer></v-spacer>
                                                <span>{{ parseFloat(dataDetails.delivery_cost).toFixed(2) }}</span>
                                            </v-row>
                                        </v-card-text>
                                    </v-card>

                                    <v-card class="mb-2">
                                        <v-card-text>
                                            <v-row class="ma-0">
                                                <span>{{'extra_costs'.split('_').join(' ') | capitalize }}</span>
                                                <v-spacer></v-spacer>
                                                <span>{{  dataDetails.extra_costs|| '-'  }}</span>
                                            </v-row>
                                        </v-card-text>
                                    </v-card>

                                    <v-card class="mb-2">
                                        <v-card-text>
                                            <v-row class="ma-0">
                                                <span>{{ $t('orders_details.discount') | capitalize }}</span>
                                                <v-spacer></v-spacer>
                                                <span>-{{ parseFloat(dataDetails.total_discount).toFixed(2) }}</span>
                                            </v-row>
                                        </v-card-text>
                                    </v-card>

                                    <v-card class="mb-2">
                                        <v-card-text>
                                            <v-row class="ma-0">
                                                <span>{{ 'tip' | capitalize }}</span>
                                                <v-spacer></v-spacer>
                                                <span>{{ parseFloat(dataDetails.tip_price).toFixed(2) }}</span>
                                            </v-row>
                                        </v-card-text>
                                    </v-card>

                                    <v-card class="mb-2 total-order">
                                        <v-card-text>
                                            <v-row class="ma-0">
                                                <span class="font-weight-bold">{{ $t('orders_details.total') | capitalize }} Price</span>
                                                <v-spacer></v-spacer>
                                                <span class="font-weight-bold">{{ dataDetails.total_price }}</span>
                                            </v-row>
                                        </v-card-text>
                                    </v-card>

                                    <v-card class="mb-2">
                                        <v-card-text>
                                            <v-row class="ma-0">
                                                <span>{{ $t('orders_details.payment_method') | capitalize }}</span>
                                                <v-spacer></v-spacer>
                                                <span>{{ dataDetails.payment_method.name }}</span>
                                            </v-row>
                                        </v-card-text>
                                    </v-card>

                                    <v-card class="mb-2">
                                        <v-card-text>
                                            <v-row class="ma-0">
                                                <span>{{'pays_with'.split('_').join(' ') | capitalize }}</span>
                                                <v-spacer></v-spacer>
                                                <span>{{  dataDetails.pays_with|| '-'  }}</span>
                                            </v-row>
                                        </v-card-text>
                                    </v-card>
                                </v-col>

                                <v-col class="col-lg-5 col-12">
                                    <v-card elevation="0">
                                        <v-card-title class="delivery-info-title">
                                            <img v-show="dataDetails.order_type.code === 'delivery'" src="/images/custom/delivery-bike.svg" alt="delivery bike" class="mr-1" width="40"/>
                                            <img v-show="dataDetails.order_type.code === 'pickup'" src="/images/custom/take-away.svg" alt="take away" class="mr-1" width="40"/>
                                            <img v-show="dataDetails.order_type.code === 'dine_in'" src="/images/custom/dine-in.svg" alt="dine_in" class="mr-1" width="40"/>
                                            <h2>{{ $t('orders_details.' + dataDetails.order_type.code) | capitalize }} Information</h2>
                                        </v-card-title>
                                    </v-card>
                                    <v-card>
                                        <v-card-text class="mb-5 mb-sm-0">
                                            <v-list class="mb-sm-5 delivery-info-listing">
                                                <v-list-item>
                                                    <v-list-item-content>
                                                        <v-list-item-title><v-icon small>mdi-account</v-icon> Name</v-list-item-title>
                                                        <v-list-item-subtitle class="pl-2">{{ dataDetails.customer.name || '-' }}</v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>

                                                <v-list-item>
                                                    <v-list-item-content>
                                                        <v-list-item-title><v-icon small>mdi-map-marker</v-icon> Address</v-list-item-title>
                                                        <v-list-item-subtitle class="pl-2" v-if="dataDetails.address !== null">
                                                            {{ dataDetails.address.street }}<br>
                                                            {{ dataDetails.address.postcode }}
                                                            {{ dataDetails.address.city }}
                                                        </v-list-item-subtitle>
                                                        <v-list-item-subtitle class="pl-2" v-else>
                                                            -
                                                        </v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>

                                                <v-list-item>
                                                    <v-list-item-content>
                                                        <v-list-item-title><v-icon small>mdi-phone</v-icon> Phone</v-list-item-title>
                                                        <v-list-item-subtitle class="pl-2">{{ dataDetails.customer.phone || '-' }}</v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>

                                                <v-list-item>
                                                    <v-list-item-content>
                                                        <v-list-item-title><v-icon small>mdi-email</v-icon> Email</v-list-item-title>
                                                        <v-list-item-subtitle class="pl-2">{{ dataDetails.customer.email || '-'  }}</v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>

                                                <v-list-item v-if="!dataDetails.is_asap">
                                                    <v-list-item-content>
                                                        <v-list-item-title><v-icon small>mdi-calendar</v-icon> Date</v-list-item-title>
                                                        <v-list-item-subtitle class="pl-2">{{ !!dataDetails.is_asap ? 'ASAP' : getDate(dataDetails.requested_time) }}</v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>

                                                <v-list-item>
                                                    <v-list-item-content>
                                                        <v-list-item-title><v-icon small>mdi-clock-outline</v-icon> {{ dataDetails.order_type.code | capitalize }} Time</v-list-item-title>
                                                        <v-list-item-subtitle class="pl-2">{{ !!dataDetails.is_asap ? 'ASAP' : getTime(dataDetails.requested_time) }}</v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>

                                                <v-list-item>
                                                    <v-list-item-content>
                                                        <v-list-item-title><img src="/images/custom/av_timer_icon.svg"> Actual Delivery</v-list-item-title>
                                                        <v-list-item-subtitle class="pl-2">{{ dataDetails.actual_delivery_time || '-' }}</v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>

                                                <v-list-item>
                                                    <v-list-item-content>
                                                        <v-list-item-title><v-icon small>mdi-file-document</v-icon> Remarks</v-list-item-title>
                                                        <v-list-item-subtitle class="pl-2">{{ dataDetails.order_remark || '-' }}</v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>

                                                <v-list-item>
                                                    <v-list-item-content>
                                                        <v-list-item-title><img src="/images/custom/delivery_icon.svg"> {{ $t('orders_details.delivery_remarks') | capitalize }}</v-list-item-title>
                                                        <v-list-item-subtitle class="pl-2">{{ dataDetails.delivery_remarks || '-' }}</v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>

                                                <v-list-item v-show="dataDetails.order_source.order_source_type.code === 'thuisbezorgd'" elevation="0">
                                                    <v-list-item-content>
                                                        <v-list-item-title><v-icon small>mdi-information-outline</v-icon> Thuisbezorgd {{ $t('orders_details.order_id') | capitalize }}</v-list-item-title>
                                                        <v-list-item-subtitle class="pl-2">{{ JSON.parse(dataDetails.order_json).publicReference || '-' }}</v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>

                                                <v-list-item v-show="dataDetails.order_source.order_source_type.code.search('ubereats') !== -1" elevation="0">
                                                    <v-list-item-content>
                                                        <v-list-item-title><v-icon small>mdi-information-outline</v-icon> UberEats {{ $t('orders_details.order_id') | capitalize }}</v-list-item-title>
                                                        <v-list-item-subtitle class="pl-2">{{ JSON.parse(dataDetails.order_json).display_id || '-' }}</v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>
                                            </v-list>
                                        </v-card-text>
                                    </v-card>
                                </v-col>

                                <!--<v-col class="col-lg-6 col-6">
                                    <v-card elevation="0" height="100%">
                                        <v-card-title>
                                            {{ 'Other Details' | capitalize }}
                                        </v-card-title>
                                        <v-card-text>
                                            <v-list class="mb-5">
                                                <v-list-item>
&lt;!&ndash;                                                    <v-list-item-action><v-icon small>mdi-account</v-icon></v-list-item-action>&ndash;&gt;
                                                    <v-list-item-content>
                                                        <v-list-item-subtitle class="pl-2">
                                                            <span class="font-weight-bold pl-1 pr-2">{{'Distance' | capitalize }}:</span>
                                                            {{ dataDetails.distance|| '-' }}
                                                        </v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>
                                                <v-divider inset></v-divider>
                                                <v-list-item>
&lt;!&ndash;                                                    <v-list-item-action><v-icon small>mdi-account</v-icon></v-list-item-action>&ndash;&gt;
                                                    <v-list-item-content>
                                                        <v-list-item-subtitle class="pl-2">
                                                            <span class="font-weight-bold pl-1 pr-2">{{'extra cost vat amount' | capitalize }}:</span>
                                                            {{ dataDetails.extra_cost_vat_amount|| '-' }}
                                                        </v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>
                                                <v-divider inset></v-divider>

                                                <v-list-item>
&lt;!&ndash;                                                    <v-list-item-action><v-icon small>mdi-account</v-icon></v-list-item-action>&ndash;&gt;
                                                    <v-list-item-content>
                                                        <v-list-item-subtitle class="pl-2">
                                                            <span class="font-weight-bold pl-1 pr-2">{{'extra_costs'.split('_').join(' ') | capitalize }}:</span>
                                                            {{ dataDetails.extra_costs|| '-' }}
                                                        </v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>
                                                <v-divider inset></v-divider>

                                                <v-list-item>
&lt;!&ndash;                                                    <v-list-item-action><v-icon small>mdi-account</v-icon></v-list-item-action>&ndash;&gt;
                                                    <v-list-item-content>
                                                        <v-list-item-subtitle class="pl-2">
                                                            <span class="font-weight-bold pl-1 pr-2">{{'is_paid'.split('_').join(' ') | capitalize }}:</span>
                                                            {{ dataDetails.is_paid|| '-' }}
                                                        </v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>
                                                <v-divider inset></v-divider>

                                                <v-list-item>
&lt;!&ndash;                                                    <v-list-item-action><v-icon small>mdi-account</v-icon></v-list-item-action>&ndash;&gt;
                                                    <v-list-item-content>
                                                        <v-list-item-subtitle class="pl-2">
                                                            <span class="font-weight-bold pl-1 pr-2">{{'is_asap'.split('_').join(' ') | capitalize }}:</span>
                                                            {{ dataDetails.is_asap|| '-' }}
                                                        </v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>
                                                <v-divider inset></v-divider>

                                                <v-list-item>
&lt;!&ndash;                                                    <v-list-item-action><v-icon small>mdi-account</v-icon></v-list-item-action>&ndash;&gt;
                                                    <v-list-item-content>
                                                        <v-list-item-subtitle class="pl-2">
                                                            <span class="font-weight-bold pl-1 pr-2">{{'is_pos_sync'.split('_').join(' ') | capitalize }}:</span>
                                                            {{ dataDetails.is_pos_sync|| '-' }}
                                                        </v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>
                                                <v-divider inset></v-divider>

                                                <v-list-item>
&lt;!&ndash;                                                    <v-list-item-action><v-icon small>mdi-account</v-icon></v-list-item-action>&ndash;&gt;
                                                    <v-list-item-content>
                                                        <v-list-item-subtitle class="pl-2">
                                                            <span class="font-weight-bold pl-1 pr-2">{{'is_printed'.split('_').join(' ') | capitalize }}:</span>
                                                            {{ dataDetails.is_printed|| '-' }}
                                                        </v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>
                                                <v-divider inset></v-divider>

                                                <v-list-item>
&lt;!&ndash;                                                    <v-list-item-action><v-icon small>mdi-account</v-icon></v-list-item-action>&ndash;&gt;
                                                    <v-list-item-content>
                                                        <v-list-item-subtitle class="pl-2">
                                                            <span class="font-weight-bold pl-1 pr-2">{{'number_of_guests'.split('_').join(' ') | capitalize }}:</span>
                                                            {{ dataDetails.number_of_guests|| '-' }}
                                                        </v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>
                                                <v-divider inset></v-divider>

                                                <v-list-item>
&lt;!&ndash;                                                    <v-list-item-action><v-icon small>mdi-account</v-icon></v-list-item-action>&ndash;&gt;
                                                    <v-list-item-content>
                                                        <v-list-item-subtitle class="pl-2">
                                                            <span class="font-weight-bold pl-1 pr-2">{{'pays_with'.split('_').join(' ') | capitalize }}:</span>
                                                            {{ dataDetails.pays_with|| '-' }}
                                                        </v-list-item-subtitle>
                                                    </v-list-item-content>
                                                </v-list-item>
                                                <v-divider inset></v-divider>
                                            </v-list>
                                        </v-card-text>
                                    </v-card>
                                </v-col>-->
                            </v-row>

                            <v-row>
                            </v-row>
                        </v-card>

                    </v-card-text>
                </v-card>
            </v-col>
            <v-dialog
                v-model="erroDialog"
                persistent
                max-width="290"
            >
                <v-card>
                    <v-card-title class="headline">
                        Error While Updating!
                    </v-card-title>
                    <v-card-text>
                        We got an error message from UberEats API. That's mean the order is either expire or something is missing.
                        <v-spacer class="mt-1" />
                        <b>
                            Do you want to move the order into error section?
                        </b>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="green darken-1"
                            text
                            @click="erroDialog = false"
                            :disabled="status!== ''"
                        >
                            Disagree
                        </v-btn>
                        <v-btn
                            v-if="userCan('orders-update')"
                            color="green darken-1"
                            text
                            :loading="status!== ''"
                            :disabled="status!== ''"
                            @click="()=>{
                                        status = 'error';
                                        updateStatus();
                                    }"
                        >
                            Agree
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

        </v-row>
    </div>
</template>

<script>
    import mixin from "../../../helpers/mixin"
    import moment from "moment"
    import SimpleTableCard from "../../../components/dashboard/SimpleTableCard";
    import {mapActions} from "vuex";

    export default {
        components: {
            SimpleTableCard
        },
        mixins: [mixin],
        props: {
            order: {
                type: Object,
                default: null
            },
            id: {
                type: String,
                default: null
            }
        },


        data() {
            return {
                erroDialog: false,
                dialogView: false,
                dataDetails: '',
                newDateTime:'',
                status: '',
            }
        },

        created() {
            if (!this.order || true) {
                if(!this.$route.query.id){
                    this.$router.push({ name: 'dashboard-orders-accepted' });
                }
                else{
                    this.fetchDetails();
                }
            }
            else{
                this.dataDetails = this.order;
                this.newDateTime = this.dataDetails.requested_time?moment(this.dataDetails.requested_time):moment(this.dataDetails.order_date+' '+this.dataDetails.order_time);
                this.newDateTime = moment(this.newDateTime).add(5, 'minutes')
            }
        },
        methods: {
            ...mapActions('app', ['showSuccess', 'showError']),
            getDate(date) {
                return date ? moment(date).format('YYYY-MM-DD') : ''
            },

            getTime(date) {
                return date ? moment(date).format('HH:mm') : ''
            },
            fetchDetails: function () {
                const _this = this;
                axios.get('/web/v1/orders/'+this.$route.query.id)
                    .then(function (response) {
                        console.log(response);
                        _this.dataDetails = response.data.data;
                        console.log(_this.dataDetails)
                        _this.newDateTime = _this.dataDetails.requested_time?moment(_this.dataDetails.requested_time):moment(_this.dataDetails.order_date+' '+_this.dataDetails.order_time);
                        _this.newDateTime = moment(_this.newDateTime).add(5, 'minutes')
                        _this.status = '';
                    })
                    .catch(function (error) {
                        console.log(error);
                        _this.showError({message:'Error: ', error: error});
                    });
            },
            updateOrder: function () {
                const _this = this;
                var data = {
                    order_id: this.dataDetails.id,
                    requested_time : moment(this.newDateTime).format('YYYY-MM-DD HH:mm:ss')
                };
                axios.patch(`/web/v1/orders/${this.dataDetails.id}`, data)
                    .then(function (response) {
                        console.log(response);
                        if(response.data.status === 'success') {
                            // _this.showSuccess('Successfully updated.');
                            // _this.updateStatus();
                            _this.fetchDetails();
                        }
                        else{
                            _this.showError({message: "Error : ", error: {message: response.data.message}});
                        }
                    })
                    .catch(function (resp) {
                        console.log(resp);
                        _this.showError({message: "Error : ", error: {message: "Something Went Wrong."}});
                    });
            },
            updateStatus: function () {
                const _this = this;
                var data = {
                    order_id: this.dataDetails.id,
                    accepted_datetime: moment(this.newDateTime).format('YYYY-MM-DD HH:mm'),
                    not_sync: 1
                }
                axios.post(`/web/v1/orders/${this.status}/${this.dataDetails.id}`, data)
                    .then(function (response) {
                        console.log(response);
                        if(response.data.errors !== undefined){
                            _this.erroDialog = true;
                            _this.status = '';
                            _this.showError({message: "Error : ", error: {message: response.data.errors.order}});
                        }
                        else {
                            _this.erroDialog = false;
                            _this.showSuccess('Successfully updated the status.');
                            _this.updateOrder();
                        }
                        // _this.fetchDetails();
                    })
                    .catch(function (err) {
                        console.log(err);
                        // console.log(err.response.data.errors.order);
                        _this.status = '';
                        // _this.erroDialog = true;
                        _this.showError({message: "Error : ", error: {message: err.response.data.errors.order}});
                    });
            },
        },
    }
</script>

<style>
.table-style .v-data-table__wrapper table {
    border-spacing: 0 15px !important;
    border-collapse: separate; padding:0;
}
.table-style .v-data-table__wrapper table .v-data-table-header tr {
    background-color: #F6F6F6;
    border-radius: 10px;
}
.table-style .v-data-table__wrapper table .v-data-table-header tr th {
    text-transform: capitalize;
    font-size: 16px;
    font-weight: 400;
    padding: 0 25px;
}
.table-style .v-data-table__wrapper table .v-data-table-header tr th:first-child {
    border-radius: 10px 0px 0px 10px;
}
.table-style .v-data-table__wrapper table .v-data-table-header tr th:last-child {
    border-radius: 0px 10px 10px 0px;
}
.table-style .v-data-table__wrapper table tbody tr {
    background: -moz-linear-gradient(left,  rgba(246,246,246,0.5) 0%, rgba(255,255,255,1) 100%);
    background: -webkit-linear-gradient(left,  rgba(246,246,246,0.5) 0%,rgba(255,255,255,1) 100%);
    background: linear-gradient(to right,  rgba(246,246,246,0.5) 0%,rgba(255,255,255,1) 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#80f6f6f6', endColorstr='#ffffff',GradientType=1 );
    box-shadow: 0px 0px 20px rgba(0,0,0,0.05); border-radius: 10px; border: 1px solid rgba(30,30,30,0.05);
}
.table-style .v-data-table__wrapper table tbody tr td {
    font-size: 16px !important;
    font-weight: 400;
    color: #9A9A9A;
    padding: 15px 25px !important;
}
.table-style .v-data-table__wrapper table tbody tr:last-child, .table-style .v-data-table__wrapper table tbody tr:last-child:hover {
    background: #FCC815 !important;
    overflow: hidden;
}
.table-style .v-data-table__wrapper table tbody tr:last-child td {
    font-size: 18px !important;
    color: #212121;
    padding: 20px !important;
    text-align: right;
    border-radius: 10px;
}
.table-style .v-data-table__wrapper table tbody tr:last-child td .font-weight-bold {
    font-size: 24px;
    margin-left: 15px;
}
.table-style .v-data-table__wrapper table tbody tr td:last-child {
    padding-right: 75px !important;
}
.table-style .v-data-table__wrapper table thead tr th:last-child { padding-right: 55px; }
.table-style .v-data-table__wrapper table thead tr th:nth-child(4) {
    padding-right: 10px;
}
.table-style .v-data-table__wrapper table tbody tr:hover td {
    color: #1C1919;
}
.table-style .v-data-table table tbody tr:not(.v-data-table__selected):hover {
    box-shadow: 0px 0px 5px rgba(0,0,0,0.05);
    transform:inherit;
}
.table-style .media {
    display: flex;
    align-items: center;
}
.table-style .media .media-body {
    flex: 1;
    padding-left: 15px;
}
.table-style img {
    width: 50px;
}
.table-style .subtotal {
    background-color: #FCC815;
    padding: 10px 145px 10px 70px;

}
.table-style .subtotal span {
    font-size: 18px;
    color: #212121;
}
.table-style .subtotal .font-weight-bold {
    font-size: 24px;
    margin-left: 15px;
}
.card-title-style {
    background-color:#FFFFFF;
    border: 1px solid #1E1E1E0D;
    border-radius: 10px;
    box-shadow: 0 30px 30px -10px rgba(0,0,0,0.05);
}
.card-title-style h2 {
    font-size: 36px;
}
.card-title-style .v-btn {
    font-size:16px;
}
.table-title {
    margin-bottom: 0;
}
.table-title h2 {
    font-size: 24px;
}
.table-title p {
    color:rgba(51,51,51,0.30);
    font-size:16px;
    margin-bottom: 0;
}
.table-title p span {
    color:rgba(51,51,51,0.80);
}
.card-table-style .v-card {
    background: -moz-linear-gradient(left,  rgba(246,246,246,0.5) 0%, rgba(246,246,246,0.9) 50%, rgba(255,255,255,1) 100%);
    background: -webkit-linear-gradient(left,  rgba(246,246,246,0.5) 0%,rgba(246,246,246,0.9) 50%,rgba(255,255,255,1) 100%);
    background: linear-gradient(to right,  rgba(246,246,246,0.5) 0%,rgba(246,246,246,0.9) 50%,rgba(255,255,255,1) 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#80f6f6f6', endColorstr='#ffffff',GradientType=1 );
    box-shadow: 0px 0px 20px rgba(0,0,0,0.05);
    border-radius: 10px;
    border: 1px solid rgba(30,30,30,0.05);
}
.card-table-style .v-card:first-child {
    background:transparent;
    border:0;
    border-radius:0;
    box-shadow:none;
}
.card-table-style .v-card__title h2, .delivery-info-title h2 {
    font-weight: 600;
    font-size: 24px;
}
.card-table-style .v-card .v-card__text {
    font-size:16px;
    padding:25px 60px 25px 30px;
}
.card-table-style .v-card.total-order {
    background:#FCC815;
}
.card-table-style .v-card.total-order .v-card__text {
    font-size:18px;
}
.card-table-style .v-card.total-order .v-card__text .font-weight-bold:last-child {
    font-size: 24px;
}
.delivery-info-listing .v-list-item {
    padding-top: 7px;
    padding-bottom: 7px;
}
.delivery-info-listing .v-list-item .v-list-item__content {
    display: flex;
    flex-wrap:inherit;
}
.delivery-info-listing .v-list-item .v-list-item__content .v-list-item__title {
    font-size: 16px;
    color:#ACACAC;
    flex: 0 0 40%;
}
.delivery-info-listing .v-list-item .v-list-item__content .v-list-item__title i, .delivery-info-listing .v-list-item .v-list-item__content .v-list-item__title img {
    margin-right: 10px;
    color:#ACACAC;
}
.delivery-info-listing .v-list-item .v-list-item__content .v-list-item__subtitle {
    font-size: 16px;
    color: #1C1919;
}
@media only screen and (max-width:480px) {
    .table-style .v-data-table__wrapper table tbody tr td:last-child {
        padding-right: 25px !important;
    }
    .table-style .v-data-table__wrapper table thead tr th:last-child {
        padding-right: 25px;
    }
    .card-title-style h2 { font-size: 30px; }
    .table-style .v-data-table__wrapper table tbody tr td {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
    .card-table-style .v-card__title {
        padding-left: 0;
        padding-right: 0;
        font-size: 0.9rem;
    }
}
</style>
