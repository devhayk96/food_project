 <template>
    <div style="width: 100%; height: 100%" v-if="loaded">
        <v-container style="height: 400px;" v-if="$store.state.optionGroup.dataDetails.id > 1 && $store.state.optionGroup.dataDetails.name === ''">
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
        <v-row class="flex-grow-0 option-group-view" dense v-else>
            <v-col cols="12">
                <v-card>
                    <v-card-title class="pa-0">
                        <div class="row-wrap">
                            <v-row class="ma-3">
                                <v-btn
                                    dark
                                    large
                                    class="back-btn"
                                    color="custom"
                                    @click="$router.push({ name: 'optional-group-list' })"
                                >
                                    <v-icon class="back-btn-arrow">
                                        mdi-arrow-left
                                    </v-icon>
                                    Go Back
                                </v-btn>
                            </v-row>
                        </div>
                    </v-card-title>
                    <v-row>
                        <v-col cols="12">
                            <div class="header-content">
                                <div class="header-content-first-item">
                                    <v-img class="first-item-img" :src="$store.state.optionGroup.dataDetails.image" />
                                </div>
                                <div class="header-content-middle-item">
                                   <h1>{{ $store.state.optionGroup.dataDetails.name }}</h1>
                                    <p>number: {{ $store.state.optionGroup.dataDetails.number }}</p>
                                </div>
                                <div class="header-content-last-item">
                                    <p>{{ $store.state.optionGroup.dataDetails.is_active ? 'active' : 'inactive' }}</p>
                                </div>

                            </div>
                        </v-col>
                    </v-row>
                    <v-row class="d-flex align-center justify-between mt-6 mr-11 mb-2 ml-11">
                        Products
                        <v-spacer></v-spacer>
                        <v-col lg="4" sm="12" class="pa-0" >
                            <v-autocomplete
                                v-model="selectedProducts"
                                @change="addToGroup"
                                :search-input.sync="filter.name"
                                item-text="name"
                                item-value="id"
                                :eager="true"
                                :items="$store.state.optionGroup.productsList"
                                prepend-inner-icon="mdi-magnify"
                                label="Search and Add Products"
                                multiple
                                outlined
                                dense
                                class="search-input"
                            >
                                <template v-slot:selection="data">

                                </template>
                                <template v-slot:prepend-item>
                                    <v-list-item
                                        ripple
                                    >
                                        <v-row class="mt-0 font-12 ">
                                            <div class="col-4 "> <span> # </span></div>
                                            <div class="search-table-img-content col-4">
                                                <span>NAME</span>
                                            </div>
                                            <div class="col-3 "> <span>PRICE</span></div>

                                        </v-row>

                                    </v-list-item>
                                    <v-divider class="mt-2"></v-divider>
                                </template>
                                <template v-slot:item='{item}'>
                                    <v-row class="font-12">

                                        <div class="col-3 pt-1 pb-1">{{item.article_number}}</div>
                                        <div class="search-table-img-content col-4 pt-1 pb-1">
                                            <v-img  v-if="item.image" class="search-table-img" :src="item.image" alt="" />
                                            <span>{{ item.name }}</span>
                                        </div>
                                        <div class="col-2 pt-1 pb-1">
                                            {{ item.price }}
                                        </div>
                                        <div class="col-2 pt-1 pb-1">
                                            <v-img v-if="!selectedProducts.includes(item.id)"  src="/images/custom/add.svg" alt="" />
                                            <v-img v-if="selectedProducts.includes(item.id)"  src="/images/custom/add_success.svg" alt="" />
                                        </div>

                                    </v-row>
                                </template>
                            </v-autocomplete>
                        </v-col>
                    </v-row>
                    <div class="pl-10 pr-10">
                        <v-data-table
                            ref="sortableTable"
                            v-sortable-data-table
                            @sorted="changeProductsOrdering"
                            :headers="headers"
                            :items="$store.state.optionGroup.optionGroupProductsList"
                            :search="filter.name"
                            :loading="$store.state.optionGroup.optionGroupProductsList === ''"
                            loading-text="Loading... Please wait"
                            class="v-data-table"
                        >
                            <template #item.drag_action="{ item }" draggable="true">
                                <v-img
                                    class="drag-img"
                                    src="/images/custom/drag.svg"
                                ></v-img>
                            </template>
                            <template #item.name="{ item }">
                                <router-link :to="{name: 'product-manage', query: {action: 'edit', id: item.id}}" class="link">
                                    {{ item.name }}
                                </router-link>
                            </template>
                            <template #item.image="{ item }">
                                <v-img
                                    class="table-img"
                                    :src="item.image"
                                ></v-img>
                            </template>
                            <template #item.status="{ item }">
                                <span :class="parseInt(item.status) === 1 ? 'text-active' : 'text-inactive'">
                                    {{ parseInt(item.status) === 1 ? 'In Stock' : 'Out of Stock' }}
                                </span>
                            </template>
                            <template #item.actions="{ item }">
                                <v-menu
                                    left
                                    offset-y
                                >
                                    <template v-slot:activator="{ on, attrs }">
                                        <img
                                            src="/images/custom/three-dots.svg"
                                            alt="three-dots"
                                            width="20"
                                            class="mr-2 cursor-pointer dots-img"
                                            v-on="on"
                                        />
                                    </template>
                                    <v-list class="actions-list actions-menu_wrap">
                                        <v-list-item class="actions-list_item">
                                            <template>
                                                <v-btn
                                                    v-if="userCan('optional_groups-update')"
                                                    icon
                                                    @click="changeStatus(item)"
                                                >
                                                    <v-icon v-if="parseInt(item.status)" class="actions-list_icon">
                                                        mdi-circle-off-outline
                                                    </v-icon>
                                                    <v-icon v-else class="actions-list_icon">
                                                        mdi-check-circle-outline
                                                    </v-icon>
                                                    <span
                                                        class="actions-list_text">{{
                                                            parseInt(item.status) ? 'Out of Stock' : 'In Stock'
                                                        }}</span>
                                                </v-btn>
                                            </template>
                                        </v-list-item>
                                        <v-list-item class="actions-list_item">
                                            <template>
                                                <v-btn
                                                    v-if="userCan('optional_groups-update')"
                                                    icon
                                                    @click="deletingProductId = item.id; deleteDialog = true"
                                                    v-bind="item"
                                                >
                                                    <v-icon class="actions-list_icon">mdi-delete-outline</v-icon>
                                                    <span class="actions-list_text">Delete</span>
                                                </v-btn>
                                            </template>
                                        </v-list-item>
                                    </v-list>
                                </v-menu>
                            </template>
                        </v-data-table>

                        <v-col cols="12" class="pt-0" v-if="orderOfProducts.length > 0">
                            <v-btn
                                class="chancel-btn"
                                color="#4C4C4C"
                                rounded
                                text
                                x-large
                                @click="$router.push({name:'optional-group-list'})"
                            >
                                Cancel
                            </v-btn>
                            <v-btn
                                class="save-btn"
                                rounded
                                x-large
                                @click="saveProductsOrderPositions"
                            >
                                SAVE
                            </v-btn>
                        </v-col>
                    </div>
                </v-card>
            </v-col>
        </v-row>

        <v-dialog
            v-model="deleteDialog"
            persistent
            max-width="400"
        >
            <v-card>
                <v-card-text class="text-h5">{{ deleteDialogTitle }}</v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="primary darken-1"
                        text
                        @click="deleteDialog = false; deletingProductId = null"
                    >
                        Cancel
                    </v-btn>
                    <v-btn
                        color="red darken-1"
                        text
                        @click="deleteProductFromOptionGroup()"
                    >
                        Delete
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
    import {mapActions} from "vuex";
    import draggable from 'vuedraggable';
    import Sortable from 'sortablejs';

    export default {
        name: "OptionalGroupView",
        props: ['id'],
        components: {
            draggable
        },
        data() {
            return {
                filter: {
                    per_page: 25,
                    name: '',
                },
                loaded: false,
                openProductsListDialog: false,
                orderOfProducts: [],
                headers: [
                    { text: '', value: 'drag_action', sortable: false},
                    { text: 'ARTICLE NUMBER', value: 'article_number'},
                    { text: 'Name', value: 'name' },
                    { text: 'Image', value: 'image' },
                    { text: 'Price', value: 'price' },
                    { text: 'Status', value: 'status' },
                    {
                        text: 'Action',
                        align: 'end',
                        value: 'actions',
                        sortable: false
                    }
                ],
                productsListHeaders: [
                    {text: 'ARTICLE NUMBER', value: 'article_number'},
                    {text: 'name', value: 'name'},
                    {text: 'price', value: 'price'},
                    {text: '', align: 'end', value: 'actions', sortable: false}
                ],
                deletingProductId: null,
                deleteDialog: false,
                deleteDialogTitle: 'Are you sure you want to delete this product from option group?',
            }
        },
        created() {
            if (this.id === undefined) {
                this.$store.state.optionGroup.dataDetails.id = this.$route.query.id;
            } else {
                this.$store.state.optionGroup.dataDetails.id = this.id;
            }
        },
        async mounted() {
            if (this.$route.query.id > 0) {
              await this.fetchOptionGroupWithProducts();
              await  this.fetchProductsList(this.filter);
              this.orderOfProducts = [...this.$store.state.optionGroup.optionGroupProductsList];
            }
            this.loaded = true;
        },
        computed: {
            selectedProducts: {
                set(val) {
                    return val
                },
                get() {
                    let ids = [];
                    this.$store.state.optionGroup.optionGroupProductsList.map((val) => {
                        ids.push(val.id);
                    }, ids)
                    return ids;
                }
            }
        },
        directives: {
            sortableDataTable: {
                bind (el, binding, vnode) {
                    const options = {
                        animation: 150,
                        onUpdate: function (event) {
                            vnode.child.$emit('sorted', event)
                        }
                    }
                    Sortable.create(el.getElementsByTagName('tbody')[0], options)
                }
            }
        },

        methods: {
            ...mapActions('app', ['showSuccess', 'showError']),
            ...mapActions('optionGroup', ['fetchProductsList', 'fetchOptionGroupWithProducts', 'saveProductsInOptionGroup']),

            changeProductsOrdering(event) {
                const movedItem = this.$store.state.optionGroup.optionGroupProductsList.splice(event.oldIndex, 1)[0];
                this.$store.state.optionGroup.optionGroupProductsList.splice(event.newIndex, 0, movedItem);
                this.orderOfProducts = [];
                let copy = [...this.$store.state.optionGroup.optionGroupProductsList];

                copy.map((val, index) => {
                    val.pivot.drag_order = index + 1;
                    delete val.pivot.id;
                    this.orderOfProducts.push(val.pivot);
                });
            },

            changeStatus(item) {
                const status = parseInt(item.status) ? 0 : 1;
                const index = this.$store.state.optionGroup.optionGroupProductsList.indexOf(item);

                axios.patch(`/web/v1/product/${item.id}`, {status})
                    .then( response => {
                        this.$store.state.optionGroup.optionGroupProductsList[index].status = status;
                        this.showSuccess(response.data.success ? 'Updated' : 'Not updated');
                    })
                    .catch( error => {
                        console.log(error);
                        this.showError({ message:'Error: ', error: error });
                    })
            },
            addToGroup(val) {
                this.saveProductsInOptionGroup(val);
            },
            saveProductsOrderPositions() {
                axios.put(`/web/v1/optional-group-product/${this.$store.state.optionGroup.dataDetails.id}`, this.orderOfProducts)
                    .then( response => {
                        this.showSuccess(response.data.success ? 'Updated' : 'Not updated');
                        if (response.data.success) {
                            this.orderOfProducts = [];
                        }
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            deleteProductFromOptionGroup() {
                let optionGroupId = this.$store.state.optionGroup.dataDetails.id;
                axios.delete(`/web/v1/optional-group-product/${optionGroupId}?productId=${this.deletingProductId}`)
                    .then( response => {
                        if (response.data.message) {
                            let productIndex = this.$store.state.optionGroup.optionGroupProductsList.findIndex( (item, index) => {
                                if (item.id === this.deletingProductId) {
                                    return true;
                                }
                            });
                            this.$store.state.optionGroup.optionGroupProductsList.splice(productIndex, 1);

                            this.showSuccess(response.data.message);
                        } else {
                            this.showError({ message: "Error : ", error: { message: "Something Went Wrong." } });
                        }

                        this.deleteDialog = false;
                        this.deletingProductId = null;
                    })
                    .catch( error => {
                        this.deleteDialog = false;
                        this.deletingProductId = null;
                        console.log(error);
                        this.showError({ message:'Error: ', error: error });
                    });
            },
        },
        watch: {
            filter: {
                deep: true,
                handler: function (val) {
                    this.fetchProductsList(val);
                },
            }
        }
    }
</script>

 <style lang="scss" scoped>
 .font-12 {
     font-size: 12px;
 }

 .search-input {
     border: 1px solid #eee;
     border-radius: 100px;
 }
 .optional-group-list {
     .row-wrap {
         width: 100%;
     }

     .dots-img {
         cursor: pointer;
     }
 }
 .actions-list {
     border: 1px solid #eee;
     width: 120px;
     border-radius: 0;
     padding: 0;
     font-size: 12px!important;

     &_item {
         border-bottom: 1px solid #eee;
         padding-left: 5px;

         &:hover {
             background-color: #eeeeee;
         }
     }

     &_text {
         font-size: 12px;
     }

     &_icon {
         &:before {
             font-size: 14px!important;
         }
     }
 }
 .table-img {
     height: 60px;
     width: 60px;
     object-fit: cover;
     border-radius: 50%;
 }

 .text {
     &-active {
         color: #3EB53E;
     }
     &-inactive {
         color: #EB4843;
     }
 }
 .link {
     color: #0096C7;
     text-decoration: none;
     text-transform: capitalize;
 }
 .actions-list_item {
     .v-btn--icon.v-size--default {
         width: auto;
     }
     .v-btn:before {
         background-color: unset;
     }
 }
 .actions-menu_wrap {
     position: relative;
 }

 .v-list-item--link .row{
     align-items: center!important;
     padding: 0 15px;
     justify-content: space-between;
     margin-bottom: 3px;
     margin-top: 3px;
 }
 .v-list--dense {
     padding: 0;
 }

 .search-table-img-content {
     display: flex;

     .search-table-img {
         width: 40px;
         height: 40px;
         border-radius: 50%;
         margin-right: 5px;
     }
 }
.header-content{
    display: flex;
    align-items: center;
}
.header-content-first-item{
    padding-right: 16px;
    padding-left: 31px;
}
.first-item-img{
    width: 56px;
    height: 56px;
    border-radius: 50%;
}
.header-content-middle-item{
}
.header-content-middle-item h1{
    text-align: left;
    font: normal normal 600 30px/38px Quicksand;
    letter-spacing: 0px;
    color: #212121;
    opacity: 1;
}
.header-content-middle-item p {
    text-align: left;
    font: normal normal 600 14px/18px Quicksand;
    letter-spacing: 0px;
    color: #212121;
    opacity: 1;
}
.header-content-last-item{
}
.header-content-last-item p {
    text-align: left;
    font: normal normal 600 14px/18px Quicksand;
    letter-spacing: 0.35px;
    color: #3EB53E;
    opacity: 1;
}

 </style>

<style>
.ml--2 .v-input--selection-controls__input {
    margin-left: -20px;
}

.header-content {
    height: 100px;
    background: #F2F5F8;
    margin: 0 88px;
}

.back-btn {
    width: 133px;
    height: 64px;
    margin: 30px;
    background: transparent !important;
    color: #0096C7 !important;
    border: 1px solid #0096C7;
}
.form-send-container {
    margin-left: 230px;
    display: flex;
    align-items: center;
}
.text-capitalize {
    margin: 0 0 20px;
}
.send-form{
    width: 48%;
}
.upload-image {
    width: 50%;
}
#input-170 {
    line-height: 0;
}
.mdi-camera::before {
    /*content: url(http://name.png);*/
}
.save-button {
    margin-left: 230px;
}
.form-pd {
    padding: 0;
}
.v-input--selection-controls__ripple {
    display: none;
}
#input-163 {
    height: 50px !important;
}
.save-btn {
    width: 160px;
    height: 42px !important;
    font: normal normal 600 14px/18px Quicksand;
    background: #0096C7 !important;
    color: #ffffff !important;
    letter-spacing: 1.4px;
}
.chancel-btn {
    margin-right: 30px;
    font: normal normal bold 14px/18px Quicksand;
    letter-spacing: 1.4px;
    color: #4C4C4C;
    opacity: 1;
}
.form-select-img {
    cursor: pointer;
    margin-left: 100px;
}
#input-157,
#input-160,
#input-182 {
    min-height: 37px;
}

.back-btn-arrow {
    font-size: 16px !important;
    margin-right: 10px;
}
.form-description textarea {
    height: 48px !important;
}

.cursor-pointer {
    cursor: pointer;
}
.drag-img {
    width: 14px;
    height: 21px;
    cursor: move;
}

.v-menu__content  .v-list-item--link .row  {
    align-items: center !important;
    padding: 0 15px;
    justify-content: space-between;
    margin-bottom: 3px;
    margin-top: 3px;
    font-size: 12px;
}

.option-group-view .v-autocomplete {
    border: none;
}

.option-group-view .v-autocomplete .v-input__slot {
    position: absolute;
    top: -33px;
    width: 464px;
    max-height: 40px;
    min-height: 40px;
}

.option-group-view .v-autocomplete .v-input__control .v-text-field__details {
    display: none;
}

.option-group-view .v-autocomplete .v-select__slot .v-label.theme--light {
    top: 10px;
}


.option-group-view .v-autocomplete .v-input__slot .v-input__prepend-inner,
.option-group-view .v-autocomplete .v-select__slot .v-input__append-inner {
    margin-top: 10px;
}

.option-group-view .v-autocomplete__content .theme--light.v-list-item:not(.v-list-item--active):not(.v-list-item--disabled) {
    color: rgb(0, 0, 0) !important;
    font-weight: 600;
}

</style>
