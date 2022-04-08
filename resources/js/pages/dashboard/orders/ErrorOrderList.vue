<template>
    <div class="d-flex flex-grow-1 flex-column">
        <v-row class="flex-grow-0" dense>
            <v-col cols="12">
                <v-card>
                    <v-card-title>
                        Error Order List
                        <v-spacer></v-spacer>
                        <!--<v-text-field
                            v-model="search"
                            append-icon="mdi-magnify"
                            label="Search"
                            single-line
                            hide-details
                        ></v-text-field>-->
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
                    <v-container>
                        <v-row>
                            <v-col cols="2" class="py-0">
                                <div class="mt-2"><b>{{ $t('orders_accepted.filters') | uppercase }}:</b></div>
                            </v-col>
                            <v-col cols="2" class="py-0">
                                <v-menu
                                    :close-on-content-click="true"
                                    :nudge-right="40"
                                    transition="scale-transition"
                                    offset-y
                                    min-width="auto"
                                    :disabled="dataList === ''"
                                >
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-text-field
                                            v-model="date"
                                            label="Date"
                                            prepend-icon="mdi-calendar"
                                            readonly
                                            v-bind="attrs"
                                            v-on="on"
                                            :disabled="dataList === ''"
                                            clearable
                                            @input="()=>{fetchList(1)}"
                                        ></v-text-field>
                                    </template>
                                    <v-date-picker
                                        v-model="date"
                                        @input="()=>{fetchList(1)}"
                                    ></v-date-picker>
                                </v-menu>
                            </v-col>
                            <v-col cols="2" class="py-0">
                                <div class="mt-2">
                                    <v-btn
                                        color="indigo"
                                        outlined
                                        @click="reset"
                                        :disabled="dataList === ''"
                                    >
                                        Reset
                                    </v-btn>
                                </div>
                            </v-col>
                        </v-row>
                    </v-container>
                    <!--<v-row>
                        <v-card-title>Filters</v-card-title>
                        <v-card-text>
                            <v-row>
                                <v-col cols="3" class="py-0">
                                    <v-menu
                                        :close-on-content-click="true"
                                        :nudge-right="40"
                                        transition="scale-transition"
                                        offset-y
                                        min-width="auto"
                                        :disabled="dataList === ''"
                                    >
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-text-field
                                                v-model="date"
                                                label="Date"
                                                prepend-icon="mdi-calendar"
                                                readonly
                                                v-bind="attrs"
                                                v-on="on"
                                                :disabled="dataList === ''"
                                                clearable
                                                @input="fetchList"
                                            ></v-text-field>
                                        </template>
                                        <v-date-picker
                                            v-model="date"
                                            @input="fetchList"
                                        ></v-date-picker>
                                    </v-menu>
                                </v-col>
                                <v-col cols="3 py-0">
                                    <v-btn
                                        color="indigo"
                                        outlined
                                        @click="reset"
                                        :disabled="dataList === ''"
                                    >
                                        Reset
                                    </v-btn>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-row>-->
                    <v-data-table
                        :headers="headers"
                        :items="dataList === ''? []:dataList"
                        :search="search"
                        :loading="dataList === ''"
                        loading-text="Loading... Please wait"
                        class="elevation-1"
                        disable-filtering
                        disable-pagination
                        hide-default-footer
                        disable-sort
                    >
                        <template #item.order="{ item }">
                            <v-dialog scrollable min-width="300px" width="50%">
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn
                                        color="primary"
                                        dark
                                        depressed
                                        outlined
                                        v-bind="attrs"
                                        v-on="on"
                                    > {{ $t('orders_details.original_json')}} </v-btn>
                                </template>
                                <v-card>
                                    <v-card-title>
                                        <span class="headline">{{ $t('orders_details.original_json') }}</span>
                                    </v-card-title>

                                    <v-card-text>
                                        <v-container>
                                            <pre>{{ item.order }}</pre>
                                        </v-container>
                                    </v-card-text>

                                    <!--<v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn color="blue darken-1" close>Close</v-btn>
                                    </v-card-actions>-->
                                </v-card>
                            </v-dialog>
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
                                :items="[10,20,50,'All']"
                                @change="()=>{fetchList(1)}"
                            ></v-select>
                        </v-col>
                    </v-card>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script>

    import { mapState, mapActions } from "vuex";
    import mixin from '../../../helpers/mixin';

    export default {
        mixins:[mixin],
        name: "ErrorOrderList",
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
                page: 1,
                pageCount: 0,
                itemsPerPage:10,
                search: '',
                date: null,
                sortBy: 'created_at',
                sortByType: 'desc',
                headers: [
                    {
                        text: 'ID',
                        align: 'start',
                        value: 'id'
                    },
                    { text: 'Message', value: 'message' },
                    { text: 'Errors', value: 'errors' },
                    { text: 'Time', value: 'created_at' },
                    { text: 'Order', value: 'order' }
                    /*{
                        text: 'Actions',
                        align: 'end',
                        value: 'actions',
                        sortable: false
                    }*/
                ],
                dataList: '',
            }
        },

        created() {
            this.itemsPerPage = +this.getNumberOfLines() || 10
        },

        mounted() {
            this.fetchList();
        },

        beforeDestroy() {
        },

        methods: {
            ...mapActions('app', ['showSuccess', 'showError']),
            reset: function(){
                this.itemsPerPage= 10;
                this.page=1;
                this.date=null;
                this.sortBy='created_at';
                this.sortByType='desc';
                this.fetchList();
            },
            fetchList: function (page=this.page) {
                const _this = this;
                this.dataList = '';
                this.page = page;
                const params = {
                    per_page: this.itemsPerPage,
                    page: this.page,
                    date: this.date?this.date: '',
                    sortBy: this.sortBy,
                    sortByType: this.sortByType,
                };
                const queryString = new URLSearchParams(params).toString();
                axios.get(`/web/v1/orders/error?${queryString}`)
                    .then(function (response) {
                        console.log(response);
                        _this.dataList = response.data.data;
                        _this.pageCount = response.data.meta.last_page;
                    })
                    .catch(function (error) {
                        console.log(error);
                        this.showError({message:'Something Went Wrong!', error: error})
                    });
            }
        }
    }
</script>
