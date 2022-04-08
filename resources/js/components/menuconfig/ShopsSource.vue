<template>
    <v-card class="no-shadows">
    <v-card-title>
        <v-row v-if="sourceShopView">
            <v-col cols="8">
                <h3>Shop's Source</h3>
            </v-col>
            <v-col cols="4">
                <v-btn
                    v-if="userCan('menus-update')"
                    class="mx-2"
                    right
                    dark
                    outlined
                    color="primary"
                    @click="()=>{
                        sourceShopView = false
                    }"
                >
                    <v-icon small dark>
                        mdi-pencil
                    </v-icon>
                    &nbsp;
                    Edit
                </v-btn>
            </v-col>
        </v-row>
        <v-row v-else>
            <v-col cols="12" class="pb-0">
                <v-select
                    v-model="selectedSourceShop"
                    :items="selectedItemSourceShop"
                    chips
                    label="Select Shop's Sources"
                    multiple
                    outlined
                    small-chips
                ></v-select>
            </v-col>
        </v-row>
    </v-card-title>
    <v-card-text>
        <v-data-table
            :headers="headers"
            :items="sourceShopView?_.filter(dataDetails.menu_source_shops,['is_blocked',0]):selectedSourceShop"
            item-key="id"
            :show-select="false"
            :disable-pagination="true"
            :hide-default-footer="true"
            class="page__table"
        >
            <template v-slot:no-data>
                No Shop's Source is selected yet!
            </template>
            <template #item.sl_no="{ item, index }">
                {{index+1}}
            </template>
            <template #item.name="{ item }" v-if="sourceShopView">
                {{item.shop.name+'-'+item.source.name || '-'}}
            </template>
            <template #item.name="{ item }" v-else>
                {{viewNameOfSourceShop(item)}}
            </template>
        </v-data-table>
    </v-card-text>
</v-card>
</template>

<script>

import {mapActions} from "vuex";

export default {
    name:'ShopsSource',

        props:['saveEvent', 'cancelEvent', 'getSources'],

        data() {
            return {
                dataDetails: {
                    id:0,
                    name: '',
                    description: '',
                    is_blocked: false,
                    start_date: null,
                    end_date: null,
                },
                selectedSourceShop:[],
                sourceShopView: true,
                productList: '',

                headers: [
                    { text: '#', value: 'sl_no', align:'start' },
                    { text: 'Name', value: 'name' },
                ],
            }
        },
        created() {
            if(this.id === undefined) {
                this.dataDetails.id = this.$route.query.id;
            }
            else{
                this.dataDetails.id = this.id;
            }
        },
        computed :{
            selectedItemSourceShop() {
                var list = [];
                console.log(this.shopList)
                _.map(this.shopList, (s)=>{
                    _.map(s.order_sources, (ss)=>{
                        list.push({text:s.name+'-'+ss.order_source_type.name, value: {shop_id:s.id, source_id: ss.order_source_type_id}});
                    })
                })
                return list;
            },
        },
        mounted() {
            console.log(this.dataDetails.id);
            this.fetchShopList();
            if(this.dataDetails.id>0){
                this.fetchDetails();
            }
            else{
                console.log('new')
            }
        },

        beforeDestroy() {
        },

        methods: {
            ...mapActions('app', ['showSuccess', 'showError']),
            viewNameOfSourceShop: function(item){
                var shop = _.find(this.shopList, ['id',item.shop_id]);
                var source = _.find(shop.order_sources, ['order_source_type_id',item.source_id]);
                return shop.name+'-'+source.order_source_type.name;
            },

            fetchDetails:async function (view=null) {
                const _this = this;
                await axios.get('/web/v1/menu/'+this.dataDetails.id)
                    .then(function (response) {
                        console.log(response);
                        _this.dataDetails = response.data;
                        _this.selectedSourceShop = [];
                        _.map(response.data.menu_source_shops, (s)=>{
                            if(s.is_blocked ===0)
                                _this.selectedSourceShop.push({shop_id:s.shop_id, source_id: s.source_id});
                        });
                        _this.selectedCategory = [];
                        _this.dataDetails.menu_categories = _.sortBy(_this.dataDetails.menu_categories, ['weight']);
                        _.map(response.data.menu_categories, (m)=>{
                            if(m.is_blocked===0) {
                                _this.selectedCategory[m.weight-1] = m.menu_category_id;
                            }
                        });
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },

            fetchShopList: function () {
                const _this = this;
                axios.get('/web/v1/shops')
                    .then(function (response) {
                        console.log(response);
                        _this.shopList = response.data.data;
                        _this.sourceShopView = false;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },

            saveSourceShop: function (newID) {
                const _this = this;
                const id =  this.dataDetails.id == 0 ? newID : this.dataDetails.id;
                console.log(id, 'new iddd')
                axios.post('/web/v1/menu/shop-source/'+ id, this.selectedSourceShop)
                    .then(function (response) {
                        console.log(response);
                        _this.showSuccess('Successfully Updated.');
                        _this.sourceShopView = true;
                        _this.fetchDetails();
                        // _this.selectedSourceShop = response.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },

            cancel() {
                {
                    this.selectedSourceShop = [];
                    _.map(this.dataDetails.menu_source_shops, (s)=>{
                        if(s.is_blocked ===0)
                            this.selectedSourceShop.push({shop_id:s.shop_id, source_id: s.source_id});
                    });
                }
            }

        },

        watch: {
            saveEvent() {
                console.log(this.saveEvent,'events')
                this.saveSourceShop(this.saveEvent);
            },

            cancelEvent() {
                this.cancel();
            }
        }

}
</script>

<style scoped>
    .no-shadows {
        box-shadow: none !important;
    }
</style>
