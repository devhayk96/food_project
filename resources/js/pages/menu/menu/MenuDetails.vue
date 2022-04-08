<template>
    <v-container>
        <v-card height="100%" width="100%">

            <v-container style="height: 400px;" v-if="dataDetails.name === ''">
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
            <v-form v-else ref="form">
                <v-card-title class="text-capitalize">
                    <v-btn
                        class="mx-2"
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
                    Manage Details - ({{dataDetails.name}} : {{$moment(dataDetails.start_date).format('LL')}} - {{$moment(dataDetails.end_date).format('LL')}})

                    <v-btn
                        v-if="userCan('menus-update')"
                        class="mx-2"
                        dark
                        outlined
                        color="primary"
                        @click="importFromProductGroup"
                    >
                        <v-icon small dark>
                            mdi-download
                        </v-icon>
                        &nbsp;
                        Import Product and Subgroup
                    </v-btn>
                </v-card-title>

                <v-card-text>
                    <v-row>
                        <v-col cols="12">
                            <v-card>
                                <v-card-title>
                                    <v-row v-if="categoryView">
                                        <v-col cols="6">
                                            <h3>Categories</h3>
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
                                                    categoryView = false
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
                                                v-model="selectedCategory"
                                                :items="_.map(categoryList, (s)=>{
                                                    return {text:s.name, value: s.id}
                                                })"
                                                chips
                                                label="Select Category"
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
                                        :items="categoryView?_.filter(dataDetails.menu_categories,['is_blocked',0]):selectedCategory"
                                        item-key="id"
                                        :show-select="false"
                                        :disable-pagination="true"
                                        :hide-default-footer="true"
                                        class="page__table"
                                    >
                                        <template v-slot:no-data>
                                            No Category is selected yet!
                                        </template>
                                        <template v-if="categoryView" #item.sl_no="{ item, index }">
                                            {{index+1}}
                                        </template>
                                        <template v-if="categoryView" #item.name="{ item }">
                                            {{item.category.name || '-'}}
                                        </template>
                                        <template v-slot:body="props" v-if="!categoryView">
                                            <draggable
                                                :list="selectedCategory"
                                                tag="tbody"
                                            >
                                                <tr v-if="props.items.length === 0">
                                                    <td colspan="2" class="text--secondary text-center">No Category is selected yet! </td>
                                                </tr>
                                                <tr
                                                    v-else
                                                    v-for="(item, index) in props.items"
                                                    :key="index"
                                                >
                                                    <td>
                                                        <v-icon small class="page__grab-icon">
                                                            mdi-arrow-all
                                                        </v-icon>
                                                    </td>
                                                    <td> {{ _.find(categoryList,['id',item]).name || '-' }} </td>
                                                </tr>
                                            </draggable>
                                        </template>
                                    </v-data-table>
                                </v-card-text>
                                <v-card-actions v-if="!categoryView" class="float-right">
                                    <v-btn
                                        v-if="userCan('menus-update')"
                                        dark
                                        outlined
                                        color="success"
                                        @click="saveCategory"
                                    >
                                        <v-icon small dark>
                                            mdi-check
                                        </v-icon>
                                        &nbsp;
                                        SAVE
                                    </v-btn>

                                    <v-btn
                                        dark
                                        outlined
                                        color="primary"
                                        @click="()=>{
                                            selectedCategory = [];
                                            _.map(dataDetails.menu_categories, (m)=>{
                                                if(m.is_blocked===0) {
                                                    selectedCategory[m.weight-1] = m.menu_category_id;
                                                }
                                            })
                                        categoryView = true
                                        }"
                                    >
                                        <v-icon small dark>
                                            mdi-cancel
                                        </v-icon>
                                        &nbsp;
                                        Cancel
                                    </v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-col>
                        <v-col cols="12">
                            <v-card>
                                <v-card-title>
                                    Products

                                    <template>
                                        <v-row justify="center">
                                            <v-dialog
                                                v-model="manageProducts"
                                                scrollable
                                            >
                                                <template v-slot:activator="{ on, attrs }">
                                                    <v-btn
                                                        v-if="userCan('menus-update')"
                                                        color="primary"
                                                        dark
                                                        outlined
                                                        v-bind="attrs"
                                                        v-on="on"
                                                    >
                                                        <v-icon small dark>
                                                            mdi-cart-plus
                                                        </v-icon>
                                                        &nbsp;
                                                        Manage Products
                                                    </v-btn>
                                                </template>
                                                <v-card>
                                                    <v-card-title>
                                                        <v-row>
                                                            <v-col>
                                                                Select Products
                                                            </v-col>
                                                            <v-col>
                                                                <v-text-field
                                                                    v-model.lazy="search"
                                                                    append-icon="mdi-magnify"
                                                                    label="Search"
                                                                    single-line
                                                                    hide-details
                                                                ></v-text-field>
                                                            </v-col>
                                                        </v-row>
                                                    </v-card-title>
                                                    <v-divider></v-divider>
                                                    <v-card-text>
                                                        <v-data-table
                                                            :headers="mangeModalDtHeaders"
                                                            :items="productList === ''?[]:_.map(productList,(p)=>{
                                                                var sp = _.find(selectedMenuProducts, ['product_id', p.id]);
                                                                if(sp !== undefined){
                                                                    return {product_id: p.id, name: p.name, price:sp.price, categories: sp.categories}
                                                                }
                                                                    return {product_id: p.id, name: p.name, price:p.price, categories: []}
                                                                })"
                                                            :loading="productList === ''"
                                                            loading-text="fetching data..."
                                                            :show-select="true"
                                                            :single-select="false"
                                                            v-model="selectedMenuProducts"
                                                            item-key="product_id"
                                                            :disable-pagination="false"
                                                            :hide-default-footer="false"
                                                            :items-per-page="15"
                                                            class="page__table"
                                                            :search="search"
                                                        >
                                                            <template v-slot:no-data>
                                                                No Product is added yet!
                                                            </template>
                                                            <template #item.article_number="{ item }">
                                                                {{ _.find(productList, ['id', item.product_id]).article_number }}
                                                            </template>
                                                            <template #item.name="{ item }">
                                                                {{_.find(productList,['id',item.product_id]).name || '-'}}
                                                            </template>
                                                            <template #item.price="{ item }">
                                                                <v-text-field
                                                                    label="Price"
                                                                    name="price"
                                                                    prepend-icon="mdi-web"
                                                                    type="number"
                                                                    v-model="item.price"
                                                                    @change="()=>changeCategories(item)"
                                                                    :required="_.find(selectedMenuProducts, ['product_id', item.product_id]) !==undefined"
                                                                ></v-text-field>
                                                            </template>
                                                            <template #item.categories="{ item }">
                                                                <v-select
                                                                    v-model="item.categories"
                                                                    :items="_.map(_.filter(dataDetails.menu_categories,['is_blocked',0]), (s)=>{
                                                                                return {text:s.category.name, value: s.id}
                                                                            })"
                                                                    chips
                                                                    label="Select Category"
                                                                    @change="()=>changeCategories(item)"
                                                                    multiple
                                                                    outlined
                                                                    small-chips
                                                                    :rules="_.find(selectedMenuProducts, ['product_id', item.product_id]) !==undefined?textrules.select2:[]"
                                                                    :required="_.find(selectedMenuProducts, ['product_id', item.product_id]) !==undefined"
                                                                ></v-select>
                                                            </template>
                                                        </v-data-table>
                                                    </v-card-text>
                                                    <v-divider></v-divider>
                                                    <v-card-actions>
                                                        <v-btn
                                                            color="blue darken-1"
                                                            text
                                                            @click="reAssignMenuProduct"
                                                        >
                                                            Close
                                                        </v-btn>
                                                        <v-btn
                                                            v-if="userCan('menus-update')"
                                                            color="blue darken-1"
                                                            text
                                                            @click="saveMenuProduct"
                                                        >
                                                            Save
                                                        </v-btn>
                                                    </v-card-actions>
                                                </v-card>
                                            </v-dialog>
                                        </v-row>
                                    </template>
                                </v-card-title>
                                <v-card-text v-if="_.filter(dataDetails.menu_categories,['is_blocked',0]).length>0">
                                    <v-tabs v-model="tab">
                                        <v-tab :key="index" v-for="(item, index) in _.filter(dataDetails.menu_categories,['is_blocked',0])">{{item.category.name || '-'}}</v-tab>
                                    </v-tabs>
                                    <v-tabs-items v-model="tab">
                                        <v-tab-item
                                            :key="index" v-for="(item, index) in _.filter(dataDetails.menu_categories,['is_blocked',0])"
                                        >
                                            <v-data-table
                                                :headers="productHeaders"
                                                :items="productList === ''?[]:_.filter(item.products,['is_blocked',0])"
                                                :loading="productList === ''"
                                                :loading-text="'fetching the details...'"
                                                item-key="id"
                                                :show-select="false"
                                                :sort-by="['weight']"
                                                :disable-pagination="true"
                                                :hide-default-footer="true"
                                                class="page__table"
                                            >
                                                <template v-slot:no-data>
                                                    No Product is added yet!
                                                </template>
                                                <template v-slot:body="props" >
                                                    <draggable
                                                        :list="props.items"
                                                        tag="tbody"
                                                        @change="saveMenuProductOrder(props.items)"
                                                    >
                                                        <tr v-if="props.items.length === 0">
                                                            <td colspan="2" class="text--secondary text-center">No Product is added yet! </td>
                                                        </tr>
                                                        <tr
                                                            v-else
                                                            v-for="(item, index) in props.items"
                                                            :key="index"
                                                        >
                                                            <td>
                                                                <v-icon small class="page__grab-icon">
                                                                    mdi-arrow-all
                                                                </v-icon>
                                                            </td>
                                                            <td> {{ _.find(productList,['id', item.menu_product.product_id]).article_number }} </td>
                                                            <td> {{ _.find(productList,['id',item.menu_product.product_id]).name || '-' }} </td>
                                                            <td> {{ parseFloat(item.menu_product.price).toFixed(2) }} </td>
                                                        </tr>
                                                    </draggable>
                                                </template>
                                            </v-data-table>
                                        </v-tab-item>
                                    </v-tabs-items>
                                </v-card-text>
                                <v-card-text v-else>
                                    <div class="text-center my-5">
                                        No Category is added yet.
                                    </div>
                                </v-card-text>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-card-text>

            </v-form>
        </v-card>
    </v-container>
</template>
<script>
    import {mapActions} from "vuex";
    import Draggable from 'vuedraggable';

    export default {
        name: "MenuManage",
        props: ['id'],
        components: {
            Draggable,
        },
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
                search: '',
                shopList: '',
                selectedSourceShop:[],
                categoryList: '',
                selectedCategory: [],
                categoryView: true,
                sourceShopView: true,
                manageProducts: false,
                tab: '',
                selectedMenuProductIds: [],
                selectedMenuProducts: [],
                productList: '',
                textrules: {
                    select: [(v) => !!v || "Item is required"],
                    select2: [(v) =>  v.length>0 || "Item is required in select 2"],

                },

                headers: [
                    { text: '#', value: 'sl_no', align:'start' },
                    { text: 'Name', value: 'name' },
                ],

                productHeaders:[
                    { text: '#', value: 'sl_no', align:'start' },
                    { text: 'Article Number', value: 'article_number' },
                    { text: 'Name', value: 'name' },
                    { text: 'Price', value: 'price' }
                ],

                mangeModalDtHeaders:[
                    { text: 'Article Number', value: 'article_number' },
                    {text:'name', value: 'name'},
                    {text:'price', value: 'price'},
                    {text:'Category', value: 'categories'}
                ]
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
                _.map(this.shopList, (s)=>{
                    _.map(s.order_sources, (ss)=>{
                        list.push({text:s.name+'-'+ss.order_source_type.name, value: {shop_id:s.id, source_id: ss.order_source_type_id}});
                    })
                })
                return list;
            },
        },
        mounted() {
            if(this.dataDetails.id>0){
                this.fetchDetails();
                this.fetchShopList();
                this.fetchCategoryList();
                this.fetchProductList();
            }
            else{
                this.$router.back();
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

            copyProduct: function (){
                const _this = this;
                _.map(_this.selectedMenuProductIds, (pid)=>{
                    if (!_.find(_this.selectedMenuProducts, ['product_id', pid])) {
                        var product = _.find(_this.productList, ['id', pid]);
                        _this.selectedMenuProducts.push({
                            product_id: product.id,
                            price: 0,
                            categories: []
                        });
                    }
                });
            },

            reAssignMenuProduct:function(){
                const _this = this;
                _this.selectedMenuProducts = [];
                _this.selectedMenuProductIds = [];
                _.map(_this.dataDetails.menu_products, (p)=>{
                    if(p.is_blocked ===0) {
                        _this.selectedMenuProductIds.push(p.product.id);
                        _this.selectedMenuProducts.push({
                            product_id: p.product.id,
                            price: p.price,
                            categories: _.filter(_.map(_this.dataDetails.menu_categories, (mc) => {
                                return _.find(_.filter(mc.products,['is_blocked',0]), ['menu_product_id', p.id]) ?
                                    mc.id
                                    :
                                    0
                            }), function (o) {
                                return o > 0;
                            })
                        });
                    }
                });
                _this.manageProducts = false;
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
                        _this.reAssignMenuProduct();
                        _.map(response.data.menu_categories, (m)=>{
                            if(m.is_blocked===0) {
                                _this.selectedCategory[m.weight-1] = m.menu_category_id;
                            }
                        });
                        if( view && view === 'category')
                        {
                            _this.categoryView = true;
                        }
                        console.log(_this.dataDetails.menu_categories.filter(i => i.category.name == 'Mais') )
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
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            fetchCategoryList: function () {
                const _this = this;
                axios.get('/web/v1/menu-category')
                    .then(function (response) {
                        console.log(response);
                        _this.categoryList = response.data.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            fetchProductList: function () {
                const _this = this;
                axios.get('/web/v1/simple/product')
                    .then(function (response) {
                        console.log(response);
                        _this.productList = response.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            saveDetails: function () {
                const _this = this;
                if (this.$refs.form.validate()) {
                    if(_this.dataDetails.id>0){
                        this.updateData();
                    }
                    else{
                        this.addData()
                    }
                }
                else{
                    _this.showError({message:"Error : ", error: {message:"Please fill the mandatory fields."}});
                }
            },
            saveSourceShop: function () {
                const _this = this;
                axios.post('/web/v1/menu/shop-source/'+ this.dataDetails.id, this.selectedSourceShop)
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
            saveCategory:async function () {
                const _this = this;
                await axios.post('/web/v1/menu/category/'+ this.dataDetails.id, this.selectedCategory)
                    .then(function (response) {
                        console.log(response);
                        _this.fetchDetails('category');
                        _this.showSuccess('Successfully Updated.');
                        // _this.selectedSourceShop = response.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            saveMenuProduct:async function () {
                const _this = this;
                console.log(this.selectedMenuProducts,'selectedMenuProducts')
                if (this.$refs.form.validate()) {
                    await axios.post('/web/v1/menu/product/' + this.dataDetails.id, this.selectedMenuProducts)
                        .then(function (response) {
                            console.log(response);
                            _this.showSuccess('Successfully Updated.');
                            _this.manageProducts = false;
                            _this.fetchDetails();
                            // _this.selectedSourceShop = response.data;
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                }
                else{
                    _this.showError({message:"Error : ", error: {message:"Please fill the mandatory fields."}});
                }
            },

            removeMenuProduct: function (d) {
                var i = _.findIndex(this.selectedMenuProducts, ['product_id',d.value]);
                // console.log(i);
                this.selectedMenuProducts.splice(i,1);
                this.selectedMenuProductIds.splice(i,1);
                // this.showSuccess('Under Development.');
            },
            saveMenuProductOrder: function (p) {
                console.log('saving weight');
                const _this = this;
                console.log(p);
                axios.post('/web/v1/menu/product-weight', p)
                    .then(function (response) {
                        console.log(response);
                        _this.showSuccess('Successfully Updated.');
                        _this.manageProducts = false;
                        _this.fetchDetails();
                        // _this.selectedSourceShop = response.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            importFromProductGroup: function () {
                const _this = this;
                this.dataDetails.name = '';
                axios.post('/web/v1/menu/import/product-and-group', {menu_id:this.dataDetails.id})
                    .then(function (response) {
                        console.log(response);
                        _this.showSuccess('Successfully Updated.');
                        _this.manageProducts = false;
                        _this.fetchDetails();
                        // _this.selectedSourceShop = response.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            changeCategories:function (d) {
                var i = _.findIndex(this.selectedMenuProducts, ['product_id', d.product_id]);
                this.selectedMenuProducts[i] = d;
                console.log(d);
            }
        },
            watch:{
                selectedMenuProducts() {
                    console.log(this.selectedMenuProducts,'adasdasd')
                }
            }
    }
</script>
