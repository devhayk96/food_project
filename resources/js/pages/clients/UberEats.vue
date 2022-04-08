<template>
    <div class="d-flex flex-grow-1 flex-column white" style="min-height: 500px">
        <v-container style="height: 400px;" v-if="checkCredential === ''">
            <v-row
                class="fill-height"
                align-content="center"
                justify="center"
            >
                <v-col
                    class="subtitle-1 text-center"
                    cols="12"
                >
                    Checking Credentials...
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
        <v-row v-else-if="!checkCredential || _.find(this.shop.order_sources, (os) => {
                    return os.order_source_type.code === 'ubereats' && os.is_active ===1;
                }) === undefined">
            <v-col>
                <v-card min-height="100%">
                    <v-card-title class="justify-center">
                        UberEats {{!checkCredential?'Credentials Doesnt match':'Source Deactive'}}.
                    </v-card-title>
                    <v-card-text class="text-center">
                        <v-icon style="font-size:400px">mdi-alert</v-icon>
                        <v-alert
                            dense
                            outlined
                            type="error"
                        >
                            {{
                            !checkCredential?
                            'Please fix the credentials or else deactivate the source for further inconvenience.'
                            :
                            'Please set source to active.'
                            }}

                        </v-alert>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
        <v-row v-else class="flex-grow-0" dense>
            <v-col cols="4">
                <v-card min-height="500px">
                    <v-card-title>
                        Sync Menu List
                        <v-spacer></v-spacer>
                        <v-dialog
                            v-model="timeCheck"
                            scrollable
                        >
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn
                                    color="primary"
                                    dark
                                    v-bind="attrs"
                                    v-on="on"
                                    :loading="syncingMenu !== ''"
                                    :disabled="syncingMenu !== '' || uberMneu === '' || dataList.length === 0"
                                >
                                    <v-icon>mdi-sync</v-icon><v-spacer />Sync
                                </v-btn>
                            </template>
                            <v-card>
                                <v-card-title>Select Service Time</v-card-title>
                                <v-divider></v-divider>
                                <v-card-text style="max-height: 400px; overflow-y: scroll; overflow-x: hidden;">
                                    <div class="text-center" v-if="selectedMenu.length === 0">
                                        <p v-if="uberMneu === '' || dataList === ''">
                                            Fetching data. Please wait
                                        </p>
                                        <p v-else>
                                            No menu is selected yet!
                                        </p>
                                    </div>
                                    <v-tabs v-else v-model="selectedMenuId">
                                        <v-tab :key="index" v-for="(item, index) in selectedMenu">{{item.name || '-'}}</v-tab>
                                    </v-tabs>
                                    <v-tabs-items v-model="selectedMenuId">
                                        <v-tab-item
                                            :key="index" v-for="(item, index) in selectedMenu"
                                        >
                                            <v-form ref="timeForm">
                                                <template v-for="(data, day) in item.service_availability">
                                                    <v-row>
                                                        <v-col cols="12" md="2">
                                                            <h3 class="pt-1 text-no-wrap">{{ $t('shop.' + data.day_of_week.toLowerCase()) | capitalize }}</h3>
                                                        </v-col>
                                                        <v-col cols="6" md="2" class="text-left">
                                                            <v-switch
                                                                dense
                                                                inset
                                                                v-model="data.is_open"
                                                                :label="data.is_open
                                                            ? $options.filters.capitalize($t('shop.open'))
                                                            : $options.filters.capitalize($t('shop.close'))"
                                                                class="mt-1 pt-0"
                                                            ></v-switch>
                                                        </v-col>
                                                        <v-col cols="12" md="8">
                                                            <v-row v-for="(slot, index) in data.time_periods" :key="'service_time-' + day + index">
                                                                <v-col cols="12" md="4">
                                                                    <v-text-field
                                                                        v-model="slot.start_time"
                                                                        :label="'Start Time'"
                                                                        prepend-icon="mdi-clock-time-four-outline"
                                                                        dense
                                                                        type="time"
                                                                        :rules="hoursRules(data.time_periods, index, 'start_time')"
                                                                        :disabled="!data.is_open"
                                                                        required
                                                                    ></v-text-field>
                                                                </v-col>
                                                                <v-col cols="12" md="1" class="text-center">
                                                                    <h4 class="pt-1 text-no-wrap">{{ $t('shop.until') | capitalize }}</h4>
                                                                </v-col>
                                                                <v-col cols="12" md="4">
                                                                    <v-text-field
                                                                        v-model="slot.end_time"
                                                                        label="End Time"
                                                                        prepend-icon="mdi-clock-time-four-outline"
                                                                        dense
                                                                        :rules="hoursRules(data.time_periods, index, 'end_time')"
                                                                        type="time"
                                                                        :disabled="!data.is_open"
                                                                        required
                                                                    ></v-text-field>
                                                                </v-col>
                                                                <v-col v-if="userCan('clients-update')" cols="12" md="3" class="text-right">
                                                                    <!--<v-btn :disabled="!data.isDelivery"
                                                                           fab dark depressed x-small color="primary" @click="initDelivery(day, index)">
                                                                        <v-icon dark>mdi-clock-time-four-outline</v-icon>
                                                                    </v-btn>-->
                                                                    <v-btn :disabled="!data.is_open"
                                                                           fab dark depressed x-small color="red" @click="()=>{
                                                                               if(data.time_periods.length === 1){
                                                                                   data.is_open = false
                                                                               }
                                                                               else{
                                                                               data.time_periods.splice(index,1)
                                                                               }
                                                                           }">
                                                                        <v-icon dark>mdi-delete</v-icon>
                                                                    </v-btn>
                                                                    <v-btn :disabled="!data.is_open"
                                                                           fab dark depressed x-small color="green" @click="()=>{data.time_periods.push({start_time: '07:00', end_time: '23:55'})}">
                                                                        <v-icon dark>mdi-plus</v-icon>
                                                                    </v-btn>
                                                                </v-col>
                                                            </v-row>
                                                        </v-col>
                                                    </v-row>
                                                    <v-divider class="mb-3 mt-1"></v-divider>
                                                </template>
                                            </v-form>
                                        </v-tab-item>
                                    </v-tabs-items>
                                </v-card-text>
                                <v-divider></v-divider>
                                <v-card-actions>
                                    <v-btn
                                        color="blue darken-1"
                                        text
                                        @click="timeCheck = false"
                                    >
                                        Close
                                    </v-btn>
                                    <v-btn
                                        v-if="userCan('clients-update')"
                                        color="blue darken-1"
                                        text
                                        :loading="syncingMenu !== ''"
                                        :disabled="syncingMenu !== '' || uberMneu === '' || selectedMenuIds.length === 0 || dataList.length === 0"
                                        @click="syncUberEatsMenu"
                                    >
                                        Save
                                    </v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-dialog>
                        <!--<v-btn
                            color="primary"
                            class="text-center"
                            :loading="syncingMenu !== ''"
                            :disabled="syncingMenu !== '' || uberMneu === '' || selectedMenuIds.length === 0 || dataList.length === 0"
                            @click="syncUberEatsMenu"
                        >
                            <v-icon>mdi-sync</v-icon><v-spacer />Sync
                        </v-btn>-->
                        <v-spacer></v-spacer>
                        <!--<v-text-field
                            v-model="search"
                            append-icon="mdi-magnify"
                            label="Search"
                            single-line
                            hide-details
                        ></v-text-field>-->
                    </v-card-title>
                    <v-data-table
                        :headers="headers"
                        :items="dataList === ''? []
                                    :
                                    _.filter(dataList, (d)=>{
                                        return _.find(uberMneu.menus, ['id', 'm-'+d.id]) !== undefined || (d.is_source_shop_blocked == 0 && d.is_blocked == 0)
                                    })"
                        :search="search"
                        :loading="dataList === ''"
                        loading-text="Loading... Please wait"
                        disable-pagination
                        hide-default-footer
                        :items-per-page="dataList.length"
                    >
                        <template #item.image="{ item }">
                            <v-img
                                max-height="60"
                                max-width="100"
                                :src="'../../'+item.image"
                            ></v-img>
                            <!--                           <img :src='item.image' alt="cat-img" />-->
                        </template>
                        <template #item.name="{ item }">
                            {{item.name}}
                            <v-chip
                                v-if="item.is_source_shop_blocked == 1 || item.is_blocked == 1"
                                :color="`red lighten-4`"
                                class="ml-0 mr-2 black--text"
                                label
                                small
                            >
                                {{item.is_source_shop_blocked == 1? 'Removed':'Inacitve'}}
                            </v-chip>
                        </template>
                        <template #item.is_sync="{ item }">
                            <v-progress-circular  indeterminate
                                                  color="primary"
                                                  v-if="uberMneu === ''"/>
                            <v-checkbox
                                v-else
                                v-model="selectedMenuIds"
                                @change="checkMenuServiceTime"
                                :disabled="syncingMenu !== '' || uberMneu === ''
                                || (_.find(uberMneu.menus, ['id', 'm-'+item.id]) === undefined && (item.is_source_shop_blocked == 1 || item.is_blocked == 1))"
                                :value="(item.id).toString()"
                            ></v-checkbox>
                            <!-- <v-icon v-else-if="_.find(uberMneu.menus, ['id', 'm-'+item.id]) !== undefined">mdi-check</v-icon>
                             <v-icon  v-else class="red&#45;&#45;text">mdi-close</v-icon>-->
                        </template>
                        <template #item.actions="{ item }">
                            <!--<v-btn icon class="text-center"
                                   :loading="syncingMenu === item.id"
                                   :disabled="syncingMenu !== '' || uberMneu === ''"
                                   @click="()=>{syncUberEatsMenu(item.id)}"
                            >
                                <v-icon>mdi-sync</v-icon>
                            </v-btn>-->
                            <!--<v-checkbox
                                v-model="selectedMenuIds"
                                :disabled="syncingMenu !== '' || uberMneu === ''"
                                :value="(item.id).toString()"
                            ></v-checkbox>-->
                            <v-btn icon class="text-center" :to="{ name: 'menu-details', query:{ action:'details', id: item.id }}">
                                <v-icon>mdi-open-in-new</v-icon>
                            </v-btn>
                            <!--<v-btn icon class="text-center" :to="{ name: 'menu-manage', query:{ action:'edit', id: item.id }}">
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>-->
                        </template>
                    </v-data-table>
                </v-card>
            </v-col>
            <!--<v-col cols="4">
                <v-card min-height="300px">
                    <v-card-title class="text-center">Restaurant Status</v-card-title>
                    <v-card-text>
                        <v-switch
                            v-if="userCan('orders-update')"
                            v-model="uberEatsStoreStatus"
                            :error-messages="uberEatsStoreStatusReason == 'OUT_OF_MENU_HOURS' || uberEatsStoreStatusReason == 'CREDENTIAL_MISMATCH' || uberEatsStoreStatusReason == 'SOURCE_DISABLED'?uberEatsStoreStatusReason.split('_').join(' '):''"
                            dense
                            :loading="uberEatsStoreStatus === ''"
                            :disabled="uberEatsStoreStatus === '' || uberEatsStoreStatusReason == 'OUT_OF_MENU_HOURS' || uberEatsStoreStatusReason == 'CREDENTIAL_MISMATCH' || _.find(shop.order_sources, (os) => {
                    return os.order_source_type.code === 'ubereats' && os.is_active ===1;
                }) === undefined"
                            @change="changeStoreStatus"
                        >
                            <template v-slot:label>
                                <b class="black&#45;&#45;text">Uber</b>
                                <b class="green&#45;&#45;text">Eats</b>
                            </template>
                        </v-switch>

                        &lt;!&ndash;                        <strong class="text-center red&#45;&#45;text" v-if="uberEatsStoreStatusReason !== ''">{{uberEatsStoreStatusReason.split('_').join(' ') | capitalize}}</strong>&ndash;&gt;
                    </v-card-text>
                </v-card>
            </v-col>-->
            <v-col cols="8">
                <v-card
                    min-height="500px"
                >
                    <v-card-title class="text-center">
                        Store Details
                        <v-spacer />
                        <v-switch
                            v-if="userCan('clients-update')"
                            v-model="uberEatsStoreStatus"
                            :error-messages="uberEatsStoreStatusReason != '' && uberEatsStoreStatusReason != 'PAUSED_BY_RESTAURANT'?uberEatsStoreStatusReason.split('_').join(' '):''"
                            dense
                            :loading="uberEatsStoreStatus === ''"
                            :disabled="uberEatsStoreStatus === '' || uberEatsStoreStatusReason == 'CREDENTIAL_MISMATCH' || uberEatsStoreStatusReason == 'SOURCE_DISABLED'"
                            @change="changeStoreStatus"
                        >
                            <template v-slot:label>
                                <b class="black--text">Uber</b>
                                <b class="green--text">Eats</b>
                                <span v-if="uberEatsStoreStatus !== '' && !uberEatsStoreStatus" class="ml-1">(Closed)</span>
                            </template>
                        </v-switch>
                        <v-spacer />
                        <v-dialog scrollable min-width="300px" width="50%" v-if="storeDetails !== ''">
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn
                                    color="primary"
                                    dark
                                    depressed
                                    v-bind="attrs"
                                    v-on="on"
                                > RAW </v-btn>
                            </template>
                            <v-card>
                                <v-card-title>
                                    <span class="headline">Store Details</span>
                                </v-card-title>

                                <v-card-text>
                                    <v-container>
                                        <pre>{{ storeDetails }}</pre>
                                    </v-container>
                                </v-card-text>
                            </v-card>
                        </v-dialog>
                        <v-spacer />
                        <v-btn
                            color="primary"
                            dark
                            :loading="storeDetails === ''"
                            :disabled="storeDetails === ''"
                            @click="getStoreDetails"
                        >
                            <v-icon>mdi-sync</v-icon><v-spacer />Refetch
                        </v-btn>
                    </v-card-title>
                    <v-card-text style="max-height: 400px; overflow-y: scroll; overflow-x: hidden;">
                        <v-container style="height: 200px;" v-if="storeDetails === ''">
                            <v-row
                                class="fill-height"
                                align-content="center"
                                justify="center"
                            >
                                <v-col
                                    class="subtitle-1 text-center"
                                    cols="12"
                                >
                                    fetching data...
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
                        <v-row :key="key" v-for="(value, key) in storeDetails">
                            <v-col cols="12" class="font-weight-bold mb-0 pb-0">{{key.split('_').join(' ') | capitalize}} :</v-col>
                            <v-col cols="12 mt-0 pt-0">
                                <span v-if="typeof(value) !== 'object'">{{value}}</span>
                                <v-row v-else :key="sub_key" v-for="(sub_value, sub_key) in value">
                                    <v-col cols="4" class="mb-0 pb-0">{{sub_key.split('_').join(' ') | capitalize}} :</v-col>
                                    <v-col cols="8 mt-0 pt-0">
                                        {{sub_value}}
                                    </v-col>
                                </v-row>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12">
                <v-card min-height="300px">
                    <v-card-title class="text-center">
                        Choose Holiday
                        <v-spacer />
                        <v-btn
                            v-if="userCan('clients-update')"
                            color="primary"
                            dark
                            :loading="holidayHours === ''"
                            :disabled="holidayHours === '' || holidayHours.length === 0"
                            @click="setHolidayHours"
                        >
                            <v-icon>mdi-sync</v-icon><v-spacer />Sync
                        </v-btn>
                    </v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col>
                                <v-menu
                                    :close-on-content-click="false"
                                    :nudge-right="40"
                                    transition="scale-transition"
                                    offset-y
                                    min-width="auto"
                                >
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-text-field
                                            :value="holidayHours ===''?'':_.map(holidayHours, (d)=>{return d.date})"
                                            label="Select Date"
                                            prepend-icon="mdi-calendar"
                                            readonly
                                            :loading="holidayHours === ''"
                                            :disabled="holidayHours === ''"
                                            v-bind="attrs"
                                            v-on="on"
                                        ></v-text-field>
                                    </template>
                                    <v-date-picker
                                        v-model="selectedDate"
                                        :allowed-dates="allowedDates"
                                        no-title
                                        scrollable
                                        :min="$moment().format('YYYY-MM-DD')"
                                        @input="()=>{
                                           holidayHours.push({date: selectedDate, open_time_periods:[{start_time: '00:00',end_time: '00:00' }]});
                                           selectedDate = '';
                                        }"
                                    ></v-date-picker>
                                </v-menu>
                            </v-col>
                            <!-- <v-col>
                                 <v-btn color="primary"
                                        @click="()=>{
                                    holidayHours.push({date: selectedDate,open_time_periods:[{start_time: '',end_time: '' }]});
                                    selectedDate = null;
                                }">
                                     ADD
                                 </v-btn>
                             </v-col>-->
                        </v-row>
                        <v-container style="height: 200px;" v-if="holidayHours === ''">
                            <v-row
                                class="fill-height"
                                align-content="center"
                                justify="center"
                            >
                                <v-col
                                    class="subtitle-1 text-center"
                                    cols="12"
                                >
                                    fetching data...
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
                        <template v-else v-for="(data, day) in holidayHours">
                            <v-row>
                                <v-col cols="12" md="2">
                                    <h3 class="pt-1 text-no-wrap">{{ data.date }}</h3>
                                </v-col>
                                <v-col cols="12" md="8" v-if="data.open_time_periods">
                                    <v-row v-for="(slot, index) in data.open_time_periods" :key="'holiday_time-' + day + index">
                                        <v-col cols="12" md="4">
                                            <v-text-field
                                                v-model="slot.start_time"
                                                :label="'Opening Hour'"
                                                prepend-icon="mdi-clock-time-four-outline"
                                                dense
                                                type="time"
                                                :rules="(data.open_time_periods.length ==1 && slot.start_time=='00:00' && slot.end_time=='00:00')? [true]: hoursRules(data.open_time_periods, index, 'start_time')"
                                                required
                                            ></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="1" class="text-center">
                                            <h4 class="pt-1 text-no-wrap">{{ $t('shop.until') | capitalize }}</h4>
                                        </v-col>
                                        <v-col cols="12" md="4">
                                            <v-text-field
                                                v-model="slot.end_time"
                                                label="Closing Hour"
                                                prepend-icon="mdi-clock-time-four-outline"
                                                dense
                                                :rules="(data.open_time_periods.length ==1 && slot.start_time=='00:00' && slot.end_time=='00:00')? [true]: hoursRules(data.open_time_periods, index, 'end_time')"
                                                type="time"
                                                required
                                            ></v-text-field>
                                        </v-col>
                                        <v-col v-if="userCan('clients-update')" cols="12" md="3" class="text-right">
                                            <!--<v-btn :disabled="!data.isDelivery"
                                                   fab dark depressed x-small color="primary" @click="initDelivery(day, index)">
                                                <v-icon dark>mdi-clock-time-four-outline</v-icon>
                                            </v-btn>-->
                                            <v-btn v-if="data.open_time_periods.length === 1"
                                                   fab dark depressed x-small color="red" @click="()=>{holidayHours.splice(day,1)}">
                                                <v-icon dark>mdi-delete</v-icon>
                                            </v-btn>
                                            <v-btn v-else
                                                   fab dark depressed x-small color="red" @click="()=>{data.open_time_periods.splice(index,1)}">
                                                <v-icon dark>mdi-delete</v-icon>
                                            </v-btn>
                                            <v-btn
                                                fab dark depressed x-small color="green" @click="()=>{data.open_time_periods.push({start_time: '', end_time: ''})}">
                                                <v-icon dark>mdi-plus</v-icon>
                                            </v-btn>
                                        </v-col>
                                    </v-row>
                                </v-col>
                                <v-col cols="12" md="8" v-else>
                                    <v-btn color="primary" @click="$nextTick(function() {data.open_time_periods=[{start_time: '00:00',end_time: '00:00' }]})">Specify opening hour</v-btn>
                                </v-col>
                            </v-row>
                        </template>

                    </v-card-text>
                    <v-card-actions>

                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script>


    import {mapActions, mapState} from "vuex";

    export default {
        name: "UberEats",
        props: {
            name: {
                type: String,
                default: ''
            }
        },
        components: {

        },
        data() {
            return {
                search: '',
                headers: [
                    /*{
                        text: 'ID',
                        align: 'start',
                        value: 'id'
                    },*/
                    { text: 'Name', align: 'start', value: 'name' },
                    { text: 'Sync', value: 'is_sync' },
                    // { text: 'Start Date', value: 'start_date' },
                    // { text: 'End Date', value: 'end_date' },
                    // { text: 'Active', value: 'is_blocked' },
                    {
                        text: 'Actions',
                        align: 'end',
                        value: 'actions',
                        sortable: false
                    }
                ],
                dataList: '',
                syncingMenu: '',
                uberMneu: '',
                selectedMenuIds: [],
                timeCheck: false,
                selectedMenuId: 0,
                selectedMenu: [],
                uberEatsStoreStatus: '',
                uberEatsStoreStatusReason: '',
                selectedDate: null,
                holidayHours: '',
                checkCredential: '',
                storeDetails: ''
            }
        },

        mounted() {
            if(!!this.shop) {
                this.fetchList();
                if (_.find(this.shop.order_sources, (os) => {
                    return os.order_source_type.code === 'ubereats' && os.is_active ===1;
                }) !== undefined) {
                    this.checkUberEatsCredential();
                }
                else{
                    this.checkCredential = true;
                    this.syncingMenu = '';
                    this.uberMneu = {menus:[]};
                    this.uberEatsStoreStatus = false;
                    this.uberEatsStoreStatusReason = 'SOURCE_DISABLED';
                    this.storeDetails = {};
                }
            }
        },

        watch: {
            shop(newVal, oldVal) {
                if (newVal !== oldVal) {
                    this.fetchList();
                    if (_.find(this.shop.order_sources, (os) => {
                        return os.order_source_type.code === 'ubereats' && os.is_active ===1;
                    }) !== undefined) {
                        this.checkUberEatsCredential();
                        /*this.getUberEatsMenu();
                        this.getHolidayHours();
                        this.fetchStoreStatus();*/
                    }
                    else{
                        this.checkCredential = true;
                        this.syncingMenu = '';
                        this.uberMneu = {menus:[]};
                        this.uberEatsStoreStatus = false;
                        this.uberEatsStoreStatusReason = 'SOURCE_DISABLED';
                        this.storeDetails = {};
                    }
                }
            },
        },

        beforeDestroy() {
        },
        computed:{
            ...mapState('app', ['shop']),
            defaultTimePush: function(){
                return {
                    start_time: '07:00', end_time: '23:55'
                }
            },
            defaultServiceTimePush: function () {
                var days = ['monday', 'tuesday', 'wednesday', 'friday', 'saturday', 'sunday'];
                return _.map(days, (d)=>{return {day_of_week: d, time_periods: [{start_time: '07:00', end_time: '23:55'}]}});
            }
        },

        methods: {
            ...mapActions('app', ['showSuccess', 'showError']),
            checkUberEatsCredential:function () {
                var _this = this;
                let data = {
                    shop_id: this.shop.id,
                    source_code: 'ubereats',
                };
                _this.checkCredential = '';
                axios.post('/web/v1/store/check/credentials', data).then(function (response) {
                    console.log(response);
                    _this.checkCredential = response.data.status;
                    if(_this.checkCredential){
                        _this.getUberEatsMenu();
                        _this.getHolidayHours();
                        _this.fetchStoreStatus();
                        _this.getStoreDetails();
                    }
                })
                    .catch(function (error) {
                        console.log(error);
                        _this.checkCredential = false;
                        _this.showError({message: 'Failed!', error: error});
                    });
            },
            allowedDates: function (val) {
                return _.find(this.holidayHours, ['date', val]) === undefined
            },
            hoursRules: function(all_time, i, key){
                var flag = false;
                if (all_time.length === 1
                    && all_time[i].start_time == '00:00'
                    && all_time[i].end_time == '00:00'
                ) {
                    return [true];
                }
                if (key === 'start_time') {
                    if (all_time[i][key] < all_time[i]['end_time']) {
                        flag = true;
                    }
                    else if(all_time.is_open !== undefined && !all_time.is_open){
                        flag = true;
                    }
                }
                else{
                    if (all_time[i][key] > all_time[i]['start_time']) {
                        flag = true;
                    }
                    else if(all_time.is_open !== undefined && !all_time.is_open){
                        flag = true;
                    }
                }
                var last_time = '00:00';
                if (all_time.length > 1 && flag) {
                    if(all_time.is_open !== undefined && !all_time.is_open){
                        flag = true;
                    }
                    else {
                        for (var j = 0; j < all_time.length; j++) {
                            if (all_time[j].start_time <= last_time || all_time[j].start_time >= all_time[j].end_time) {
                                flag = false;
                                break;
                            } else {
                                last_time = all_time[j].end_time;
                            }
                        }
                    }
                }
                return [flag];
            },
            checkMenuServiceTime: function(){
                var _this = this;
                var days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                var newMenuList = [];
                _.map(this.selectedMenuIds, (id)=>{
                    var menu =_.find(_this.selectedMenu, ['id', id]);
                    if(menu !== undefined){
                        newMenuList.push(menu);
                    }
                    else{
                        menu =_.find(_this.uberMneu.menus, ['id', 'm-'+id]);
                        if(menu !== undefined){
                            newMenuList.push({id: id, service_availability: menu.service_availability, name: menu.title.translations.en})
                        }
                        else{
                            newMenuList.push({id: id, service_availability: _.map(days, (d)=>{return {is_open: true, day_of_week: d, time_periods: [{start_time: '00:00', end_time: '00:00'}]}}),name:_.find(_this.dataList, ['id', parseInt(id)]).name});
                        }
                    }
                });
                this.selectedMenu = [];
                this.selectedMenu = newMenuList;
            },
            fetchList: function () {
                const _this = this;
                _this.dataList = '';
                const params = {
                    source_code: 'ubereats',
                    shop_id: this.shop.id
                }
                const queryString = new URLSearchParams(params).toString();
                axios.get(`/web/v1/menu/source-shop?${queryString}`)
                    .then(function (response) {
                        console.log(response);
                        _this.dataList = response.data.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                        _this.showError({message:'Failed!', error: error});
                    });
            },

            getUberEatsMenu: function () {
                var _this = this;
                _this.uberMneu = '';
                _this.selectedMenuIds = [];
                _this.selectedMenu = [];
                let data = {
                    shop_id: this.shop.id,
                    source_code: 'ubereats',
                }
                axios.post('/web/v1/store/menu/get', data).then(function (response) {
                    console.log(response);
                    if(response.data.message) {
                        _this.showError({message: "Error : ", error: {message: response.data.message}});
                    }
                    _this.uberMneu = response.data;
                    var days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                    _.map(response.data.menus, (m)=>{
                        if(_.find(_this.dataList, ['id', parseInt((m.id).split('-')[1])]) !== undefined) {
                            _this.selectedMenuIds.push((m.id).split('-')[1]);
                            _this.selectedMenu.push({
                                id: (m.id).split('-')[1],
                                name: m.title.translations.en,
                                service_availability: _.map(days, (d) => {
                                    var s = _.find(m.service_availability, ['day_of_week', d]);
                                    if (s === undefined) {
                                        return {
                                            is_open: false,
                                            day_of_week: d,
                                            time_periods: [{start_time: '00:00', end_time: '00:00'}]
                                        }
                                    }
                                    return {...s, is_open: true}
                                })
                            })
                        }
                    });
                    _this.syncingMenu = '';
                })
                    .catch(function (error) {
                        console.log(error);
                        _this.showError({message:'Failed!', error: error});
                    });
            },

            syncUberEatsMenu: function () {
                var _this = this;
                var flag = true;
                for (var i = 0; i < this.selectedMenu.length; i++) {
                    if(!flag){
                        break;
                    }
                    for (var j = 0; j < this.selectedMenu[i].service_availability.length; j++) {
                        var last_time = '00:00';
                        if(!flag){
                            break;
                        }
                        if(this.selectedMenu[i].service_availability[j].is_open) {
                            if(this.selectedMenu[i].service_availability[j].time_periods.length === 1
                                && this.selectedMenu[i].service_availability[j].time_periods[0].start_time == '00:00'
                                && this.selectedMenu[i].service_availability[j].time_periods[0].end_time == '00:00'
                            ){
                                continue;
                            }
                            for (var k = 0; k < this.selectedMenu[i].service_availability[j].time_periods.length; k++) {
                                if (this.selectedMenu[i].service_availability[j].time_periods[k].start_time <= last_time || this.selectedMenu[i].service_availability[j].time_periods[k].start_time >= this.selectedMenu[i].service_availability[j].time_periods[k].end_time) {
                                    flag = false;
                                    break;
                                } else {
                                    last_time = this.selectedMenu[i].service_availability[j].time_periods[k].end_time;
                                }
                            }
                        }
                    }
                }
                if(flag) {
                    this.syncingMenu = 'loading';
                    let data = {
                        shop_id: this.shop.id,
                        source_code: 'ubereats',
                        menu_ids: this.selectedMenuIds,
                        menu_list: _.map(this.selectedMenu, (m)=>{return{...m,service_availability: _.filter(m.service_availability, ['is_open', true])}})
                    }
                    axios.post('/web/v1/store/menu/sync', data).then(function (response) {
                        console.log(response);
                        _this.syncingMenu = '';
                        if (response.data.status) {
                            _this.showSuccess('Sync completed successfully');
                            _this.timeCheck = false;
                            _this.getUberEatsMenu();
                        } else {
                            _this.showError({message: 'Failed!', error: {message: response.data.response}});
                        }
                    })
                        .catch(function (error) {
                            console.log(error);
                            _this.syncingMenu = '';
                            _this.showError({message: 'Failed!', error: error});
                        });
                }
                else{
                    _this.showError({message: 'Validation Error!', error: {message: 'Please fixed the overlapping time.'}});
                }
            },
            fetchStoreStatus: function () {
                const _this = this;
                this.uberEatsStoreStatus = '';
                this.uberEatsStoreStatusReason = '';
                axios.get(`/web/v1/store/status/${this.shop.id}/ubereats`)
                    .then(function (response) {
                        console.log(response);
                        if(response.data.message) {
                            _this.showError({message: "Error : ", error: {message: response.data.message}});
                        }
                        if (response.data.status == 'ONLINE') {
                            _this.uberEatsStoreStatus = true;
                        }
                        else {
                            _this.uberEatsStoreStatus = response.data.offlineReason != 'PAUSED_BY_RESTAURANT';
                            _this.uberEatsStoreStatusReason = response.data.offlineReason == 'OUT_OF_MENU_HOURS'?'OUTSIDE_OPENING_HOURS': response.data.offlineReason;
                            /*if(response.data.offlineReason == 'OUT_OF_MENU_HOURS'){
                                _this.uberEatsStoreStatus = true;
                                _this.uberEatsStoreStatusReason = 'OUTSIDE_OPENING_HOURS';
                            }
                            else {
                                _this.uberEatsStoreStatus = false;
                                _this.uberEatsStoreStatusReason = response.data.offlineReason;
                            }*/
                        }
                    })
                    .catch(function (resp) {
                        console.log(resp);
                        _this.showError({message: "Error : ", error: {message: "Something Went Wrong."}});
                    });
            },
            changeStoreStatus: function () {
                const _this = this;
                var data = {
                    shop_id:this.shop.id,
                    source_code: 'ubereats',
                    status: this.uberEatsStoreStatus? 'ONLINE': 'PAUSED'
                }
                this.uberEatsStoreStatus = '';
                this.uberEatsStoreStatusReason = '';
                axios.post(`/web/v1/store/status/change`, data)
                    .then(function (response) {
                        console.log(response);
                        // _this.uberEatsStoreStatus = data.status === 'ONLINE';
                        _this.showSuccess('Successfully updated the store status.');
                        _this.fetchStoreStatus();
                    })
                    .catch(function (resp) {
                        console.log(resp);
                        _this.showError({message: "Error : ", error: {message: "Something Went Wrong."}});
                        _this.uberEatsStoreStatus = !_this.uberEatsStoreStatus;
                    });
            },
            getHolidayHours: function () {
                var _this = this;
                _this.selectedDate = null;
                _this.holidayHours = '';
                let data = {
                    shop_id: this.shop.id,
                    source_code: 'ubereats',
                }
                axios.post('/web/v1/store/holiday-hours/get', data).then(function (response) {
                    console.log(response);
                    if(response.data.message) {
                        _this.showError({message: "Error : ", error: {message: response.data.message}});
                    }
                    _this.holidayHours = _.map(response.data.holiday_hours, (d, i)=>{
                        return {date: i, open_time_periods: d.open_time_periods}
                    });
                })
                    .catch(function (error) {
                        console.log(error);
                        _this.showError({message:'Failed!', error: error});
                    });
            },
            getStoreDetails: function () {
                var _this = this;
                let data = {
                    shop_id: this.shop.id,
                    source_code: 'ubereats',
                }
                this.storeDetails = '';
                axios.post('/web/v1/store/source/details', data).then(function (response) {
                    console.log(response);
                    if(response.data.message) {
                        _this.showError({message: "Error : ", error: {message: response.data.message}});
                    }
                    _this.storeDetails = response.data;
                })
                    .catch(function (error) {
                        console.log(error);
                        _this.showError({message:'Failed!', error: error});
                    });
            },


            setHolidayHours: function () {
                var _this = this;
                var flag = this.holidayHours.length>0;
                for (var i = 0; i < this.holidayHours.length; i++) {
                    if(!flag){
                        break;
                    }
                    if(this.holidayHours[i].open_time_periods) {
                        if(this.holidayHours[i].open_time_periods.length === 1
                            && this.holidayHours[i].open_time_periods[0].start_time === '00:00'
                            && this.holidayHours[i].open_time_periods[0].end_time === '00:00'
                        ){
                            continue;
                        }
                        var last_time = '00:00';
                        for (var j = 0; j < this.holidayHours[i].open_time_periods.length; j++) {
                            if (this.holidayHours[i].open_time_periods[j].start_time <= last_time
                                || this.holidayHours[i].open_time_periods[j].start_time >= this.holidayHours[i].open_time_periods[j].end_time) {
                                flag = false;
                                break;
                            } else {
                                last_time = this.holidayHours[i].open_time_periods[j].end_time;
                            }
                        }
                    }
                }
                if(flag) {
                    let data = {
                        shop_id: this.shop.id,
                        source_code: 'ubereats',
                        holiday_hours: this.holidayHours
                    }
                    this.holidayHours = '';
                    axios.post('/web/v1/store/holiday-hours/set', data).then(function (response) {
                        console.log(response);
                        if (response.data.status) {
                            _this.showSuccess('Sync completed successfully');
                            _this.getHolidayHours();
                        } else {
                            _this.holidayHours= data.holiday_hours;
                            _this.showError({message: 'Failed!', error: {message: response.data.response}});
                        }
                    })
                        .catch(function (error) {
                            console.log(error);
                            _this.holidayHours= data.holiday_hours;
                            _this.showError({message: 'Failed!', error: error});
                        });
                }
                else{
                    _this.showError({message: 'Validation Error!', error: {message: 'Please fixed the overlapping time.'}});
                }
            },
        }
    }
</script>
