<template>
    <v-container>
        <v-card height="100%" width="100%">
            <v-container style="height: 400px;" v-if="dataDetails.id > 0 && dataDetails.name === ''">
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
            <v-form ref="form" v-else>
                <v-card-title class="text-capitalize">
                    {{ dataDetails.id > 0 ? 'Edit': 'Add' }} Kitchen
                </v-card-title>

                <v-card-text>
                    <v-row>
                        <v-col cols="6" md="6">
                            <v-row>
                                <v-col cols="12" md="12">
                                    <v-text-field
                                        label="Name"
                                        name="name"
                                        prepend-icon="mdi-account-switch"
                                        type="text"
                                        v-model="dataDetails.name"
                                        :rules="textRules"
                                        required
                                    ></v-text-field>
                                    <div class="red--text" v-if="$store.state.app.errors.name">{{ $store.state.app.errors.name }}</div>
                                </v-col>
                                <v-col cols="12" md="12">
                                    <v-text-field
                                        label="Number"
                                        name="number"
                                        prepend-icon="mdi-account-switch"
                                        type="text"
                                        v-model="dataDetails.number"
                                        :rules="textRules"
                                        required
                                    ></v-text-field>
                                    <div class="red--text" v-if="$store.state.app.errors.number">{{ $store.state.app.errors.number }}</div>
                                </v-col>

                                <v-col cols="12" md="12">
                                    <v-select
                                        label="Shop"
                                        name="shop"
                                        item-text="name"
                                        item-value="id"
                                        prepend-icon="mdi-select"
                                        :loading="shopList === ''"
                                        :items="shopList === '' ? [] : shopList"
                                        v-model="dataDetails.shop_id"
                                        required
                                    ></v-select>
                                    <div class="red--text" v-if="$store.state.app.errors.shop_id">{{ $store.state.app.errors.shop_id }}</div>
                                </v-col>

                                <v-col cols="3" md="3">
                                    <v-switch
                                        v-model="dataDetails.is_blocked"
                                        inset
                                        :label="`Blocked`"
                                    ></v-switch>
                                </v-col>

                                <v-col cols="12" md="12">
                                    <v-btn
                                        v-if="userCan(savePermission)"
                                        dark
                                        bottom
                                        right
                                        large
                                        color="success"
                                        @click="saveDetails"
                                    >
                                        SAVE
                                    </v-btn>
                                </v-col>
                            </v-row>
                        </v-col>

                        <v-col cols="6" md="6">
                            <v-row>
                                <v-col cols="12" md="12">
                                    <div class="pl-10 pr-10">
                                        <v-autocomplete
                                            v-model="kitchenSubGroupList"
                                            @change="saveKitchenSubGroup"
                                            :search-input.sync="filter.name"
                                            item-text="name"
                                            item-value="id"
                                            :loading="subGroupList === ''"
                                            :items="subGroupList === '' ? [] : subGroupList"
                                            prepend-inner-icon="mdi-magnify"
                                            label="Search and Add SubGroups"
                                            multiple
                                            outlined
                                            dense
                                            class="search-input"
                                        >
                                            <template v-slot:selection="data">

                                            </template>
                                            <template v-slot:prepend-item>
                                                <v-list-item ripple>
                                                    <v-row class="mt-0 font-12 ">
                                                        <div class="col-2 "> <span> # </span></div>
                                                        <div class="search-table-content col-6"><span>NAME</span></div>
                                                        <div class="col-3 "> <span>&nbsp;</span></div>
                                                    </v-row>
                                                </v-list-item>
                                                <v-divider class="mt-2"></v-divider>
                                            </template>
                                            <template v-slot:item='{item}'>
                                                <v-row class="search-table-content font-12">
                                                    <div class="col-2 pt-1 pb-1">{{ item.id }}</div>
                                                    <div class="col-6 pt-1 pb-1">
                                                        <v-img v-if="item.image" class="search-table-img" :src="item.image" alt="" />
                                                        <span>{{ item.name }}</span>
                                                    </div>
                                                    <div class="col-3 pt-1 pb-1 add-img">
                                                        <v-img v-if="kitchenSubGroupList.includes(item)"  src="/images/custom/add_success.svg" alt="" />
                                                        <v-img v-else src="/images/custom/add.svg" alt="" />
                                                    </div>
                                                </v-row>
                                            </template>
                                        </v-autocomplete>
                                    </div>
                                </v-col>

                                <v-col cols="12" md="12">
                                    <div class="pl-10 pr-10">
                                        <v-data-table
                                            ref="sortableTable"
                                            v-sortable-data-table
                                            @sorted="changeSubGroupsOrdering"
                                            :headers="headers"
                                            :items="kitchenSubGroupList"
                                            :search="filter.name"
                                            :loading="kitchenSubGroupList === ''"
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
                                                <router-link :to="{name: 'product-subgroup-manage', query: {action: 'edit', id: item.id}}" class="link">
                                                    {{ item.name }}
                                                </router-link>
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
                                                                    icon
                                                                    @click="deletingSubgroupId = item.id; deleteDialog = true"
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

                                        <v-col cols="12" class="pt-0" v-if="orderOfSubGroups.length > 0">
                                            <v-btn
                                                class="chancel-btn"
                                                color="#4C4C4C"
                                                rounded
                                                text
                                                x-large
                                                @click="$router.push({name: 'kitchen-list'})"
                                            >
                                                Cancel
                                            </v-btn>
                                            <v-btn
                                                class="save-btn"
                                                rounded
                                                x-large
                                                @click=""
                                            >
                                                SAVE
                                            </v-btn>
                                        </v-col>
                                    </div>

                                </v-col>
                            </v-row>
                        </v-col>
                    </v-row>
                </v-card-text>

            </v-form>
        </v-card>


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
                        @click="deleteDialog = false; deletingSubgroupId = null"
                    >
                        Cancel
                    </v-btn>
                    <v-btn
                        color="red darken-1"
                        text
                        @click="deleteSubgroupFromKitchen()"
                    >
                        Delete
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script>

import {mapActions, mapMutations} from "vuex";
    import draggable from 'vuedraggable';
    import Sortable from 'sortablejs';

    export default {
        name: "KitchenManage",
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
                orderOfSubGroups: [],
                kitchenSubGroupList: [],
                dataDetails: {
                    id:0,
                    name: '',
                    number: '',
                    shop_id: '',
                    is_blocked: 0,
                    subGroupList: []
                },
                headers: [
                    { text: '', value: 'drag_action', sortable: false},
                    { text: 'Name', value: 'name' },
                    { text: 'Action', align: 'end', value: 'actions', sortable: false }
                ],
                subGroupList: '',
                shopList: '',
                textRules: [
                    v => !!v || 'Field is required'
                ],
                savePermission: 'kitchens-create',
                deletingSubgroupId: null,
                deleteDialog: false,
                deleteDialogTitle: 'Are you sure you want to delete this subgroup from kitchen?',
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
        mounted() {
            if(this.dataDetails.id>0){
                this.savePermission = 'kitchens-update';
                this.fetchDetails()
            }
            this.fetchShopList();
            this.setErrors();
        },

        beforeDestroy() {
        },

        watch: {
            filter: {
                deep: true,
                handler: function (val) {
                    this.fetchSubGroupList(val);
                },
            }
        },

        methods: {
            ...mapActions('app', ['showSuccess', 'showError', 'showValidationErrors']),
            ...mapMutations('app', ['setErrors']),
            fetchShopList() {
                axios.get('/web/v1/shops')
                    .then( ({data}) => {
                        this.shopList = data.data;
                    })
                    .catch( resp => {
                        console.log(resp);
                    });
            },
            fetchDetails() {
                axios.get('/web/v1/kitchen/'+ this.dataDetails.id)
                    .then( ({data}) => {
                        this.dataDetails = data.data;
                        this.kitchenSubGroupList = this.dataDetails.subGroupList;
                    })
                    .catch( error => {
                        this.showError({message:'Error: ', error: error});
                    });
            },
            fetchSubGroupList() {
                axios.get(`/web/v1/subgroup`)
                    .then( ({data}) => {
                        this.subGroupList = data.data;
                    })
                    .catch( error => {
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
            addData() {
                axios.post('/web/v1/kitchen', this.dataDetails)
                    .then( response => {
                        if (response.data) {
                            this.showSuccess('Kitchen is created.');
                            this.$refs.form.reset();
                            this.$refs.form.resetValidation();
                            this.$router.push({name: 'kitchen-list'});
                        } else {
                            this.showError({message: "Error : ", error: {message: "Something Went Wrong."}});
                        }
                    })
                    .catch( error => {
                        if (error.response.status === 422) {
                            this.showValidationErrors(error.response.data.errors);
                        } else {
                            this.showError({message:'Error: ', error: error});
                        }
                        console.log(error);
                    });
            },
            updateData() {
                axios.put('/web/v1/kitchen/' + this.dataDetails.id, this.dataDetails)
                    .then( response => {
                        if (response.data) {
                            this.showSuccess('Kitchen is updated.');
                            this.$refs.form.reset();
                            this.$refs.form.resetValidation();
                            this.$router.push({name: 'kitchen-list'});
                        } else {
                            this.showError({message: "Error : ", error: {message: "Something Went Wrong."}});
                        }
                    })
                    .catch( error => {
                        console.log(error);
                        this.showError({message:'Error: ', error: error});
                    });
            },

            changeSubGroupsOrdering(event) {

            },

            deleteSubgroupFromKitchen() {
                let subgroupIndex = this.kitchenSubGroupList.findIndex( (item) => {
                    if (item.id === this.deletingSubgroupId) {
                        return true;
                    }
                });
                console.log(subgroupIndex);
                this.kitchenSubGroupList.splice(subgroupIndex, 1);

                this.dataDetails.subGroupList = [...this.kitchenSubGroupList];
                this.deleteDialog = false;
                this.deletingSubgroupId = null;
            },
            saveKitchenSubGroup(groupIds) {
                this.kitchenSubGroupList = [];
                this.subGroupList.find( (subgroup) => {
                    if (groupIds.includes(subgroup.id)) {
                        this.kitchenSubGroupList.push(subgroup);
                    }
                });
                this.dataDetails.subGroupList = this.kitchenSubGroupList;
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
    }
</script>

<style lang="scss">
    .drag-img {
        width: 15px;
        cursor: pointer;
    }

    .add-img {
        .v-responsive__sizer {
            padding-bottom: 30px !important;
        }

        .v-image__image--cover {
            background-size: auto;
        }
    }

    .search-table-content {
        display: flex;
        align-items: center;
    }

    .v-data-table table tbody tr {
        max-height: 50px;
    }
</style>
