<template>
    <div
        v-shortkey="['ctrl', '/']"
        class="d-flex flex-grow-1"
        @shortkey="onKeyup"
    >
        <!-- Navigation -->
        <v-navigation-drawer
            v-model="drawer"
            app
            floating
            class="elevation-1"
            :right="$vuetify.rtl"
            :light="menuTheme === 'light'"
            :dark="menuTheme === 'dark'"
        >
            <!-- Navigation menu info -->
            <template v-slot:prepend>
                <div class="pa-2">
                    <div class="title font-weight-bold text-uppercase primary--text">{{ product.name }}</div>
                    <!--                    <div class="overline grey&#45;&#45;text">v{{ product.version }}</div>-->
                    <!-- <div  class="overline grey&#45;&#45;text">{{$moment(product.timeStamp).format('LLL')}}
                        &lt;!&ndash; <v-tooltip bottom>
                             <template v-slot:activator="{ on, attrs }">
                                 <b
                                     v-bind="attrs"
                                     v-on="on"
                                 >
                                     {{$moment(product.timeStamp).fromNow()}}
                                 </b>
                             </template>
                             <span>{{$moment(product.timeStamp).format('LLL')}}</span>
                         </v-tooltip>&ndash;&gt;
                     </div>-->
                </div>
            </template>

            <!-- Navigation menu -->
            <main-menu :menu="navigation.menu"/>

            <!-- Navigation menu footer -->
            <template v-slot:append>
                <!-- Footer navigation links -->
                <div class="pa-1 text-center">
                    <v-btn
                        v-for="(item, index) in navigation.footer"
                        :key="index"
                        :href="item.href"
                        :target="item.target"
                        small
                        text
                    >
                        {{ item.key ? $t(item.key) : item.text }}
                    </v-btn>
                </div>
            </template>
        </v-navigation-drawer>

        <!-- Toolbar -->
        <v-app-bar
            app
            :color="isToolbarDetached ? 'surface' : undefined"
            :flat="isToolbarDetached"
            :light="toolbarTheme === 'light'"
            :dark="toolbarTheme === 'dark'"
            style='z-index:9999;'
        >
            <v-card class="flex-grow-1 d-flex" :class="[isToolbarDetached ? 'pa-1 mt-3 mx-1' : 'pa-0 ma-0']"
                    :flat="!isToolbarDetached">
                <div class="d-flex flex-grow-1 align-center">

                    <!-- search input mobile -->
                    <v-text-field
                        v-if="showSearch"
                        append-icon="mdi-close"
                        placeholder="Search"
                        prepend-inner-icon="mdi-magnify"
                        hide-details
                        solo
                        flat
                        autofocus
                        @click:append="showSearch = false"
                    ></v-text-field>

                    <div v-else class="d-flex flex-grow-1 align-center">
                        <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
                        <v-autocomplete
                            :items="shopsList"
                            item-text="name"
                            item-value="id"
                            v-model="shopAutoSelected"
                            dense
                            prepend-icon="mdi-store-outline"
                            label="Select Shop"
                            class="ml-2 mt-1"
                            :hide-details=true
                            :hide-no-data=true
                            @change="updateShop"
                        ></v-autocomplete>
                        <v-layout class="mt-2" v-if="shop">
                            <v-img src="/images/custom/delivery-bike32x32.png" max-width="32" max-height="32" class="ml-5"></v-img>
                            <v-btn class="mx-2" fab dark x-small color="primary" @click="time.delivery > 5 ? $set(time, 'delivery', time.delivery - 1) : false">
                                <v-icon dark>mdi-minus</v-icon>
                            </v-btn>
                            <v-text-field dense style="max-width: 30px" v-model.number="time.delivery " @keypress="checkNumber($event)" @keyup="checkValue('delivery')"></v-text-field>
                            <v-btn class="mx-2" fab dark x-small color="primary" @click="time.delivery < 270 ? $set(time, 'delivery', time.delivery + 1) : false">
                                <v-icon dark>mdi-plus</v-icon>
                            </v-btn>
                            <div class="w24">
                                <v-scroll-x-transition>
                                    <v-icon
                                        v-if="save.delivery"
                                        color="success"
                                    >
                                        mdi-check
                                    </v-icon>
                                </v-scroll-x-transition>
                            </div>
                        </v-layout>

                        <v-layout class="mt-2" v-if="shop">
                            <v-img src="/images/custom/take-away.png" max-width="30" max-height="30" class="ml-5"></v-img>
                            <v-btn class="mx-2" fab dark x-small color="primary" @click="time.pickup > 5 ? $set(time, 'pickup', time.pickup - 1) : false">
                                <v-icon dark>mdi-minus</v-icon>
                            </v-btn>
                            <v-text-field dense style="max-width: 30px" v-model.number="time.pickup" @keypress="checkNumber($event)" @keyup="checkValue('pickup')"></v-text-field>
                            <v-btn class="mx-2" fab dark x-small color="primary" @click="time.pickup < 270 ? $set(time, 'pickup', time.pickup + 1) : false">
                                <v-icon dark>mdi-plus</v-icon>
                            </v-btn>
                            <div class="w24">
                                <v-scroll-x-transition>
                                    <v-icon
                                        v-if="save.pickup"
                                        color="success"
                                    >
                                        mdi-check
                                    </v-icon>
                                </v-scroll-x-transition>
                            </div>
                        </v-layout>
                        <v-spacer class="d-none d-lg-block"></v-spacer>

                        <!-- search input desktop -->
                        <v-text-field
                            ref="search"
                            class="mx-1 hidden-xs-only"
                            :placeholder="$t('menu.search')"
                            prepend-inner-icon="mdi-magnify"
                            hide-details
                            filled
                            rounded
                            dense
                        ></v-text-field>

                        <v-spacer class="d-block d-sm-none"></v-spacer>

                        <v-btn class="d-block d-sm-none" icon @click="showSearch = true">
                            <v-icon>mdi-magnify</v-icon>
                        </v-btn>

                        <toolbar-language/>

                        <div :class="[$vuetify.rtl ? 'ml-1' : 'mr-1']">
                            <toolbar-notifications/>
                        </div>

                        <toolbar-user/>
                    </div>
                </div>
            </v-card>
        </v-app-bar>

        <v-main>
            <v-container class="fill-height" :fluid="!isContentBoxed">
                <v-layout>
                    <slot></slot>
                </v-layout>
            </v-container>

            <v-footer app inset>
                <div class="overline grey--text">v{{ product.version }} - {{$moment(product.timeStamp).format('LLL')}}</div>
                <v-spacer></v-spacer>
                <div class="overline">
                    Built with
                    <v-icon small color="pink">mdi-heart</v-icon>
                </div>
            </v-footer>
        </v-main>
    </div>
</template>

<script>
    import {mapState, mapMutations} from 'vuex'
    import { mapActions } from "vuex"
    import moment from 'moment';

    // navigation menu configurations
    import config from '../configs'

    import MainMenu from '../components/navigation/MainMenu'
    import ToolbarUser from '../components/toolbar/ToolbarUser'
    import ToolbarApps from '../components/toolbar/ToolbarApps'
    import ToolbarLanguage from '../components/toolbar/ToolbarLanguage'
    import ToolbarCurrency from '../components/toolbar/ToolbarCurrency'
    import ToolbarNotifications from '../components/toolbar/ToolbarNotifications'

    export default {
        components: {
            MainMenu,
            ToolbarUser,
            ToolbarApps,
            ToolbarLanguage,
            ToolbarCurrency,
            ToolbarNotifications
        },
        data() {
            return {
                drawer: null,
                showSearch: false,
                navigation: config.navigation,

                time: {
                    delivery: null,
                    pickup: null
                },
                dataShop: null,
                shopsList: [],
                shopAutoSelected: null,
                timerUpdateTime: {
                    delivery: null,
                    pickup: null
                },
                timerCheckTime: {
                    delivery: null,
                    pickup: null
                },
                save: {
                    delivery: false,
                    pickup: false
                },
                intervalId: '',
            }
        },
        computed: {
            ...mapState('app', ['product', 'isContentBoxed', 'menuTheme', 'toolbarTheme', 'isToolbarDetached', 'shop', 'newOrderCount']),
        },

        created () {
            this.initialize()
        },

        beforeDestroy(){
            clearInterval(this.intervalId);
        },

        watch:{
            'time.delivery' (newVal, oldVal) {
                if (!!newVal && newVal !== oldVal && newVal !== this.dataShop.delivery_time) {
                    clearTimeout(this.timerUpdateTime.delivery);
                    this.timerUpdateTime.delivery = setTimeout(() => this.updateTime('delivery'), 1000)
                }
            },
            'time.pickup' (newVal, oldVal) {
                if (!!newVal && newVal !== oldVal && newVal !== this.dataShop.pickup_time) {
                    clearTimeout(this.timerUpdateTime.pickup);
                    this.timerUpdateTime.pickup = setTimeout(() => this.updateTime('pickup'), 1000)
                }
            }
        },

        methods: {
            ...mapMutations('app', ['setShop', 'setNewOrderCount']),
            ...mapMutations('shop', ['setShopsList']),
            ...mapActions('app', ['showSuccess']),

            onKeyup(e) {
                this.$refs.search.focus()
            },
            getCookie(cname)  {
                var name = cname + "=";
                var decodedCookie = decodeURIComponent(document.cookie);
                var ca = decodedCookie.split(';');
                for(var i = 0; i <ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) == ' ') {
                        c = c.substring(1);
                    }
                    if (c.indexOf(name) == 0) {
                        return c.substring(name.length, c.length);
                    }
                }
                return "";
            },

            setCookie(cname, cvalue, exdays) {
                var d = new Date();
                d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
                var expires = "expires="+d.toUTCString();
                document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
            },

            initialize() {
                axios.get('/web/v1/shops/list/user')
                    .then( ({data}) => {
                        this.shopsList = data.data;
                        this.setShopsList(data);
                        this.$nextTick(function () {
                            if (this.shopsList.length > 0) {
                                let id = this.getCookie('s_id');
                                if (id) {
                                    this.shopAutoSelected = parseInt(atob(id));
                                    this.updateShop(parseInt(atob(id)));
                                } else {
                                    this.shopAutoSelected = this.shopsList[0].id;
                                    this.updateShop(this.shopsList[0].id);
                                }
                            }
                        })
                    })
            },
            updateShop(value) {
                this.setCookie('s_id', btoa(value), 365);
                this.dataShop = this.shopsList.find(x => x.id === value);
                this.setShop(this.dataShop);
                this.getNewOrderCount();
                if (_.filter(this.dataShop.order_sources, ['is_active', 1]).length>0) {
                    if (this.intervalId === '') {
                        this.intervalId = setInterval(() => {
                            this.getNewOrderCount();
                        }, 10000);
                    }
                }
                else{
                    if (this.intervalId !== '') {
                        clearInterval(this.intervalId);
                    }
                }
                this.$set(this.time, 'delivery', this.dataShop.hasOwnProperty('delivery_time') ? this.dataShop.delivery_time : 5);
                this.$set(this.time, 'pickup', this.dataShop.hasOwnProperty('pickup_time') ? this.dataShop.pickup_time : 5);
            },

            getNewOrderCount() {
                let self = this;
                axios.get(`/web/v1/orders/new/count/${this.shop.id}`).then(function (response) {
                    self.setNewOrderCount(response.data);
                })
            },

            updateTime(type) {
                let dataUpdate = {
                        id: this.dataShop['id'],
                        time: this.time[type],
                        type: type
                    },
                    self = this;

                axios.put('/web/v1/shops/update/time', dataUpdate).then(function (response) {
                    self.save[type] = true;
                    setTimeout(function () {
                        self.save[type] = false
                    }, 1250)
                })
            },

            checkNumber(evt) {
                evt = (evt) ? evt : window.event;
                let charCode = (evt.which) ? evt.which : evt.keyCode;
                if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
                    evt.preventDefault();
                } else {
                    return true;
                }
            },

            checkValue(type) {
                clearTimeout(this.timerCheckTime[type])
                let self = this;
                setTimeout(function () {
                    let value = self.time[type];
                    if (value < 5) {
                        self.$set(self.time, type, 5);

                    } else if (value > 270) {
                        self.$set(self.time, type, 270);
                    }
                }, 750)
            },
        }
    }
</script>

<style scoped>
    .buy-button {
        box-shadow: 1px 1px 18px #ee44aa;
    }

    .w24 {
        width: 24px;
    }
</style>
