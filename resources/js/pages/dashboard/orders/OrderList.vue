<template>
  <div class="d-flex flex-grow-1 flex-column">
    <v-row class="flex-grow-0" dense>
      <v-col cols="12">
        <v-card>
          <v-card-title>
            <h3 class="text-capitalize">
              {{ $route.name.split("-").join(" ") }}
            </h3>
            <v-spacer></v-spacer>
            <v-col class="text-center pt-0" cols="3">
              <v-switch
                v-if="userCan('clients-update')"
                v-model="uberEatsStoreStatus"
                :error-messages="
                  uberEatsStoreStatusReason != '' &&
                  uberEatsStoreStatusReason != 'PAUSED_BY_RESTAURANT'
                    ? uberEatsStoreStatusReason.split('_').join(' ')
                    : ''
                "
                dense
                :loading="uberEatsStoreStatus === ''"
                :disabled="
                  uberEatsStoreStatus === '' ||
                  uberEatsStoreStatusReason == 'CREDENTIAL_MISMATCH' ||
                  uberEatsStoreStatusReason == 'SOURCE_DISABLED'
                "
                @change="changeStoreStatus"
              >
                <template v-slot:label>
                  <b class="black--text">Uber</b>
                  <b class="green--text">Eats</b>
                  <span
                    v-if="uberEatsStoreStatus !== '' && !uberEatsStoreStatus"
                    class="ml-1"
                    >(Closed)</span
                  >
                </template>
              </v-switch>
            </v-col>
            <v-spacer></v-spacer>
            <v-col cols="2" class="py-0">
              <v-select
                v-model="sortBy"
                label="Sort By"
                :items="[{ text: 'Order', value: 'order_datetime' }]"
                @change="
                  () => {
                    fetchList(1);
                  }
                "
                :disabled="dataList === ''"
              ></v-select>
            </v-col>
            <v-col cols="2" class="py-0">
              <v-select
                v-model="sortByType"
                label="Type"
                :items="['desc', 'asc']"
                @change="
                  () => {
                    fetchList(1);
                  }
                "
                :disabled="dataList === ''"
              ></v-select>
            </v-col>
          </v-card-title>
          <v-data-table
            :headers="headers"
            :items="dataList === '' ? [] : dataList"
            class="elevation-1"
            :loading="dataList === ''"
            loading-text="Loading... Please wait"
            disable-filtering
            hide-default-footer
            disable-pagination
            disable-sort
          >
            <template v-slot:top>
              <v-container>
                <v-row>
                  <v-col
                    ><div class="mt-1">
                      <b>{{ $t("orders_accepted.filters") | uppercase }}:</b>
                    </div></v-col
                  >
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
                          @input="
                            () => {
                              fetchList(1);
                            }
                          "
                          :disabled="dataList === ''"
                        ></v-text-field>
                      </template>
                      <v-date-picker
                        v-model="filters.date"
                        no-title
                        scrollable
                        @input="
                          () => {
                            fetchList(1);
                          }
                        "
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
                  <v-col
                    cols="2"
                    class="text-center"
                    v-if="
                      $route.name !== 'order-new' &&
                      $route.name !== 'order-denied'
                    "
                  >
                    <v-select
                      :items="statusItems === '' ? [] : statusItems"
                      item-text="name"
                      item-value="value"
                      :label="$t('orders_accepted.status') | capitalize"
                      dense
                      v-model="filters.status"
                      :disabled="dataList === ''"
                      multiple
                      @change="
                        () => {
                          fetchList(1);
                        }
                      "
                    >
                      <template v-slot:selection="{ item, index }">
                        <v-chip v-if="index === 0">
                          <span>{{ item.name }}</span>
                        </v-chip>
                        <span v-if="index === 1" class="black--text caption">
                          +{{ filters.status.length - 1 }}
                        </span>
                      </template>
                    </v-select>
                  </v-col>

                  <v-col class="text-center">
                    <v-select
                      :items="sourceItems === '' ? [] : sourceItems"
                      item-text="name"
                      item-value="value"
                      :label="$t('orders_accepted.source') | capitalize"
                      dense
                      v-model="filters.source"
                      :loading="sourceItems === ''"
                      :disabled="dataList === ''"
                      @change="
                        () => {
                          fetchList(1);
                        }
                      "
                    ></v-select>
                  </v-col>

                  <v-col class="text-center">
                    <v-select
                      :items="paymentItems === '' ? [] : paymentItems"
                      item-text="name"
                      item-value="value"
                      :label="$t('orders_accepted.payment') | capitalize"
                      dense
                      v-model="filters.payment"
                      :loading="paymentItems === ''"
                      :disabled="dataList === ''"
                      @change="
                        () => {
                          fetchList(1);
                        }
                      "
                    ></v-select>
                  </v-col>

                  <v-col class="text-center">
                    <v-select
                      :items="typeItems === '' ? [] : typeItems"
                      item-text="name"
                      item-value="value"
                      :label="$t('orders_accepted.type') | capitalize"
                      dense
                      v-model="filters.type"
                      :loading="typeItems === ''"
                      :disabled="dataList === ''"
                      @change="
                        () => {
                          fetchList(1);
                        }
                      "
                    ></v-select>
                  </v-col>

                  <v-col class="text-center">
                    <v-select
                      :items="posItems === '' ? [] : posItems"
                      item-text="name"
                      item-value="value"
                      label="Pos Sync"
                      dense
                      v-model="filters.isPos"
                      :disabled="dataList === ''"
                      @change="
                        () => {
                          fetchList(1);
                        }
                      "
                    ></v-select>
                  </v-col>

                  <v-col class="text-center pt-0" cols="2">
                    <v-switch
                      v-model="autoRefresh"
                      dense
                      :label="`Auto Refresh in ${intervalTime - counter} sec`"
                      :disabled="dataList === ''"
                    ></v-switch>
                  </v-col>
                  <v-col class="text-center">
                    <v-select
                      v-model="intervalTime"
                      label="Interval Time"
                      dense
                      :items="[10, 30, 90, 300]"
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

            <template #item.order_number="{ item, index }">
              {{ item.order_number || "-" }}
            </template>

            <!--<template #item.order_number="{ item, index }">
                            {{
                                item.order_number?
                                    sortByType === 'desc'?
                                        page*(itemsPerPage)-itemsPerPage+(index+1)
                                        :
                                        item.order_number
                                    :
                                    '-'
                            }}
                        </template>-->
            <template #item.time="{ item }">{{ getValueTime(item) }}</template>

            <template v-slot:item.actions="{ item }">
              <div>
                <v-btn
                  icon
                  class="text-center"
                  :to="{
                    name: 'dashboard-orders-details',
                    params: { order: item },
                    query: { id: item.id },
                  }"
                >
                  <v-icon>mdi-open-in-new</v-icon>
                </v-btn>
                <v-menu
                  icon
                  bottom
                  class="text-center"
                  v-if="$route.name === 'order-new' && false"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn icon v-bind="attrs" v-on="on">
                      <v-icon>mdi-dots-vertical</v-icon>
                    </v-btn>
                  </template>

                  <v-list v-if="userCan('orders-update')">
                    <v-list-item>
                      <v-list-item-title
                        ><a @click="() => acceptOrder(item.id)"
                          >Accept Order</a
                        ></v-list-item-title
                      >
                    </v-list-item>
                    <v-list-item>
                      <v-list-item-title
                        ><a @click="() => denyOrder(item.id)"
                          >Deny Order</a
                        ></v-list-item-title
                      >
                    </v-list-item>
                    <v-list-item>
                      <v-list-item-title>
                        <a
                          @click="
                            () => {
                              editedOrder = item;
                            }
                          "
                          >Update Time</a
                        ></v-list-item-title
                      >
                    </v-list-item>
                    <v-list-item>
                      <v-list-item-title>
                        <v-dialog v-model="dialog">
                          <template v-slot:activator="{ on, attrs }">
                            <a
                              class="mb-2"
                              v-bind="attrs"
                              v-on="on"
                              @click="
                                () => {
                                  dialog = true;
                                  newDate = item.requested_time
                                    ? $moment(item.requested_time).format(
                                        'YYYY-MM-DD'
                                      )
                                    : $moment(item.order_datetime).format(
                                        'YYYY-MM-DD'
                                      );
                                  newTime = item.requested_time
                                    ? $moment(item.requested_time).format(
                                        'HH:MM:SS'
                                      )
                                    : $moment(item.order_datetime).format(
                                        'HH:MM:SS'
                                      );
                                }
                              "
                            >
                              Update Time
                            </a>
                          </template>
                          <v-card>
                            <v-card-title class="headline"
                              >Update Pickup/Delivery Time</v-card-title
                            >
                            <v-card-text>
                              <v-container>
                                <v-row>
                                  <v-col cols="12" md="6">
                                    <v-date-picker
                                      v-model="newDate"
                                      label="Pickup/Delivery Date"
                                    ></v-date-picker>
                                  </v-col>
                                  <v-col cols="12" md="6">
                                    <v-time-picker
                                      v-model="newTime"
                                      label="Pickup/Delivery Time"
                                      use-seconds
                                    ></v-time-picker>
                                  </v-col>
                                </v-row>
                              </v-container>
                            </v-card-text>
                            <v-card-actions>
                              <v-spacer></v-spacer>
                              <v-btn
                                color="blue darken-1"
                                @click="
                                  () => {
                                    dialog = false;
                                  }
                                "
                                text
                                >Cancel</v-btn
                              >
                              <v-btn
                                color="blue darken-1"
                                @click="
                                  () => {
                                    updateOrder(item.id);
                                  }
                                "
                                text
                                >Update</v-btn
                              >
                              <v-spacer></v-spacer>
                            </v-card-actions>
                          </v-card>
                        </v-dialog>
                      </v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </div>
            </template>

            <template v-slot:no-data>
              <v-btn color="primary" @click="reset">Reset</v-btn>
            </template>
          </v-data-table>
          <v-card class="d-flex justify-space-between pa-4" style="gap: 10rem">
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
                v-bind:label="$t('$vuetify.dataFooter.itemsPerPageText')"
                :items="[10, 20, 50, $t('$vuetify.dataFooter.itemsPerPageAll')]"
                @change="
                  () => {
                    fetchList(1);
                  }
                "
              ></v-select>
            </v-col>
          </v-card>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import mixin from "../../../helpers/mixin";
import { mapActions, mapState } from "vuex";
import moment from "moment";

export default {
  mixins: [mixin],
  data: () => ({
    page: 1,
    pageCount: 0,
    itemsPerPage: 10,
    name: null,
    search: "",
    sortBy: "order_datetime",
    sortByType: "desc",
    sortList: [
      { text: "Order Number", value: "order_number" },
      { text: "Date", value: "created_at" },
      { text: "Name", value: "name" },
    ],
    intervalId: null,
    intervalTime: 30,
    counter: 0,
    autoRefresh: true,
    dialog: false,
    newDate: "",
    newTime: "",
    filters: {
      status: [],
      source: "",
      payment: "",
      type: "",
      isPos: "",
      date: moment().format("YYYY-MM-DD"),
    },
    statusItems: [{ name: "All", value: "" }],
    sourceItems: [{ name: "All", value: "" }],
    paymentItems: [{ name: "All", value: "" }],
    typeItems: [{ name: "All", value: "" }],
    shopItems: [{ name: "All", value: "" }],
    posItems: [
      { name: "All", value: "" },
      { name: "Sync", value: "1" },
      { name: "Not Sync", value: "0" },
    ],
    dataList: "",
    uberEatsStoreStatus: "",
    uberEatsStoreStatusReason: "",
  }),

  computed: {
    ...mapState("app", ["shop"]),
    headers() {
      return [
        {
          text: this.$t("orders_accepted.st"),
          value: "order_status.name" || "order_status.code",
        },
        {
          text: "#",
          value: "daily_order_number",
        },
        {
          text: _.capitalize(this.$t("orders_accepted.date")),
          value: "order_date",
          filter: (value) => {
            if (!this.filters.date) return true;
            return value === this.filters.date;
          },
          align: " d-none",
        },
        {
          text: _.capitalize(this.$t("orders_accepted.received")),
          value: "order_time",
        },
        {
          text: _.capitalize(this.$t("orders_accepted.name")),
          value: "customer.name",
        },
        {
          text: _.capitalize(this.$t("orders_accepted.address")),
          value: "address.street",
        },
        {
          text: _.capitalize(this.$t("orders_accepted.postcode")),
          value: "address.postcode",
        },
        {
          text: _.capitalize(this.$t("orders_accepted.city")),
          value: "address.city",
        },
        {
          text: _.capitalize(this.$t("orders_accepted.phone")),
          value: "customer.phone",
        },
        {
          text: _.capitalize(this.$t("orders_accepted.amount")),
          value: "total_price",
        },
        {
          text: _.capitalize(this.$t("orders_accepted.source")),
          value:
            "order_source.order_source_type.name" ||
            "order_source.order_source_type.code",
        },
        {
          text: _.capitalize(this.$t("orders_accepted.payment")),
          value: "payment_method.name" || "payment_method.code",
        },
        {
          text: _.capitalize(this.$t("orders_accepted.type")),
          value: "order_type.name" || "order_type.code",
        },
        {
          text: _.capitalize(this.$t("orders_accepted.time")),
          value: "time",
        },
        {
          text: _.capitalize(this.$t("orders_accepted.shop")),
          value: "shop.name",
        },
        {
          text: "Sync",
          value: "is_pos_sync",
        },
        {
          text: _.capitalize(this.$t("orders_accepted.actions")),
          align: "end",
          value: "actions",
          sortable: false,
        },
      ];
    },
  },

  watch: {
    shop(newVal, oldVal) {
      if (newVal !== oldVal) {
        this.fetchOrderStatusList();
        if (
          _.find(this.shop.order_sources, (os) => {
            return (
              os.order_source_type.code === "ubereats" && os.is_active === 1
            );
          }) !== undefined
        ) {
          this.fetchStoreStatus();
        } else {
          this.uberEatsStoreStatus = false;
          this.uberEatsStoreStatusReason = "SOURCE_DISABLED";
        }
      }
    },
    $route(to, from) {
      if (to.name !== from.name) {
        this.fetchOrderStatusList();
        if (
          _.find(this.shop.order_sources, (os) => {
            return (
              os.order_source_type.code === "ubereats" && os.is_active === 1
            );
          }) !== undefined
        ) {
          this.fetchStoreStatus();
        } else {
          this.uberEatsStoreStatus = false;
          this.uberEatsStoreStatusReason = "SOURCE_DISABLED";
        }
      }
    },
  },

  updated() {
    // console.log(this.$route.name);
  },

  created() {
    // set number of lines
    this.itemsPerPage = +this.getNumberOfLines() || 10;
    //query checking
    if (!_.isEmpty(this.$route.query)) this.queryCheck();
    if (!!this.shop) {
      this.fetchOrderStatusList();
      if (
        this.$route.name !== "order-new" &&
        this.$route.name !== "order-accepted" &&
        this.$route.name !== "order-denied"
      ) {
        this.fetchList();
      }
      if (
        _.find(this.shop.order_sources, (os) => {
          return os.order_source_type.code === "ubereats" && os.is_active === 1;
        }) !== undefined
      ) {
        this.fetchStoreStatus();
      } else {
        this.uberEatsStoreStatus = false;
        this.uberEatsStoreStatusReason = "SOURCE_DISABLED";
      }
    }
    this.fetchSourceTypeList();
    this.fetchPaymentMethodList();
    this.fetchOrderTypeList();
    // this.filters.date = moment().format();
  },

  mounted() {},

  beforeDestroy() {
    clearInterval(this.intervalId);
  },

  methods: {
    ...mapActions("app", ["showSuccess", "showError"]),
    queryCheck: function () {
      if (this.$route.query.hasOwnProperty("status"))
        this.filters.status = this.$route.query.status;
      if (this.$route.query.hasOwnProperty("source"))
        this.filters.source = this.$route.query.source;
      if (this.$route.query.hasOwnProperty("payment"))
        this.filters.payment = this.$route.query.payment;
      if (this.$route.query.hasOwnProperty("type"))
        this.filters.type = this.$route.query.type;
      if (this.$route.query.hasOwnProperty("isPos"))
        this.filters.isPos = this.$route.query.isPos;
      if (this.$route.query.hasOwnProperty("date"))
        this.filters.date = this.$route.query.date;
      if (this.$route.query.hasOwnProperty("page"))
        this.page = parseInt(this.$route.query.page);
      if (this.$route.query.hasOwnProperty("per_page"))
        if (this.$route.query.hasOwnProperty("sortBy"))
          // this.itemsPerPage = parseInt(this.$route.query.per_page);
          this.sortBy = this.$route.query.sortBy;
      if (this.$route.query.hasOwnProperty("sortByType"))
        this.sortByType = this.$route.query.sortByType;
    },
    autoRefreshCheck: function () {
      let self = this;
      if (this.autoRefresh) {
        this.autoRefresh = false;
        // this.dataList = '';
        const params = {
          key: "qazplm@123",
        };
        const queryString = new URLSearchParams(params).toString();
        axios
          .get(`/web/v1/orders/pos/pull?${queryString}`)
          .then(function (resp) {
            console.log(resp);
            self.reset();
          })
          .catch(function (err) {
            console.log(err);
          });
      }
    },
    reset: function () {
      this.itemsPerPage = +this.getNumberOfLines() || 10;
      this.page = 1;
      this.sortBy = "order_datetime";
      this.sortByType = "desc";
      this.filters = {
        status:
          this.$route.name !== "order-new" &&
          this.$route.name !== "order-accepted" &&
          this.$route.name !== "order-denied"
            ? []
            : this.filters.status,
        source: "",
        payment: "",
        type: "",
        isPos: "",
        date: moment().format("YYYY-MM-DD"),
      };
      /* if (!!this.intervalId) {
                     clearInterval(this.intervalId);
                 }*/
      this.counter = 0;
      this.autoRefresh = true;
      this.fetchList();
    },
    fetchList(page = this.page) {
      let self = this;
      /*if (!!this.intervalId) {
                    clearInterval(this.intervalId);
                }*/
      // this.dataList = '';
      this.page = page;
      const params = {
        ...this.filters,
        // per_page: this.itemsPerPage,
        page: this.page,
        sortBy: this.sortBy,
        sortByType: this.sortByType,
      };
      const queryString = new URLSearchParams(params).toString();
      // console.log(params);
      if (
        this.$route.name !== "order-new" &&
        this.$route.name !== "order-denied"
      ) {
        this.$router
          .push({
            name: this.$route.name,
            query: params,
          })
          .catch((error) => {
            if (error.name != "NavigationDuplicated") {
              throw error;
            }
          });
      } else {
        var filters = JSON.parse(JSON.stringify(this.filters));
        delete filters["status"];
        this.$router
          .push({
            name: this.$route.name,
            query: {
              ...filters,
              // per_page: this.itemsPerPage,
              page: this.page,
              sortBy: this.sortBy,
              sortByType: this.sortByType,
            },
          })
          .catch((error) => {
            if (error.name != "NavigationDuplicated") {
              throw error;
            }
          });
      }
      // console.log(params);
      // console.log(queryString);
      axios
        .get(`/web/v1/orders/all/shop/${this.shop.id}?${queryString}`)
        .then(function (response) {
          console.log(response);
          if (self.intervalId === null) {
            self.intervalId = setInterval(() => {
              if (self.autoRefresh) {
                if (self.counter === self.intervalTime) {
                  self.autoRefreshCheck();
                } else {
                  self.counter++;
                }
              } else {
                self.counter = 0;
                // clearInterval(self.intervalId);
              }
            }, 1000);
          } else {
            self.counter = 0;
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
      _this.filters.status = [];
      _this.dataList = "";
      if (_this.$route.name === "order-accepted") {
        _this.statusItems = [];
      }
      axios
        .get("/web/v1/order-statuses")
        .then(function (response) {
          console.log(response);
          _.map(response.data.data, (item) => {
            if (item.parent_id === null) {
              if (_this.$route.name === "order-accepted") {
                if (item.code !== "new" && item.code !== "declined") {
                  _this.statusItems.push({ name: item.name, value: item.id });
                }
              } else {
                _this.statusItems.push({ name: item.name, value: item.id });
              }
              // console.log(item.id);

              if (
                _this.$route.name === "order-accepted" &&
                item.code !== "new" &&
                item.code !== "declined" &&
                item.code !== "cancelled" &&
                item.code !== "error"
              ) {
                _this.filters.status.push(item.id);
              } else if (
                _this.$route.name === "order-new" &&
                item.code === "new"
              ) {
                _this.filters.status.push(item.id);
                _this.reset();
              } else if (
                _this.$route.name === "order-denied" &&
                item.code === "declined"
              ) {
                _this.filters.status.push(item.id);
                _this.reset();
              }
            }
          });
          if (_this.$route.name === "order-accepted") {
            _this.reset();
          }
        })
        .catch(function (resp) {
          console.log(resp);
        });
    },
    fetchStoreStatus: function () {
      const _this = this;
      _this.uberEatsStoreStatus = "";
      _this.uberEatsStoreStatusReason = "";
      axios
        .get(`/web/v1/store/status/${this.shop.id}/ubereats`)
        .then(function (response) {
          console.log(response);
          if (response.data.message) {
            _this.showError({
              message: "Error : ",
              error: { message: response.data.message },
            });
          }
          if (response.data.status == "ONLINE") {
            _this.uberEatsStoreStatus = true;
          } else {
            _this.uberEatsStoreStatus =
              response.data.offlineReason != "PAUSED_BY_RESTAURANT";
            _this.uberEatsStoreStatusReason =
              response.data.offlineReason == "OUT_OF_MENU_HOURS"
                ? "OUTSIDE_OPENING_HOURS"
                : response.data.offlineReason;
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
          _this.showError({
            message: "Error : ",
            error: { message: "Something Went Wrong." },
          });
        });
    },
    changeStoreStatus: function () {
      const _this = this;
      var data = {
        shop_id: this.shop.id,
        source_code: "ubereats",
        status: this.uberEatsStoreStatus ? "ONLINE" : "PAUSED",
      };
      this.uberEatsStoreStatus = "";
      axios
        .post(`/web/v1/store/status/change`, data)
        .then(function (response) {
          console.log(response);
          // _this.uberEatsStoreStatus = data.status === 'ONLINE';
          _this.showSuccess("Successfully updated the store status.");
          _this.fetchStoreStatus();
        })
        .catch(function (resp) {
          console.log(resp);
          _this.showError({
            message: "Error : ",
            error: { message: "Something Went Wrong." },
          });
          _this.uberEatsStoreStatus = !_this.uberEatsStoreStatus;
        });
    },
    fetchSourceTypeList: function () {
      const _this = this;
      axios
        .get("/web/v1/order-source-types")
        .then(function (response) {
          console.log(response);
          // _this.sourceItems.push({ name: _this.$t('common.all'), value: '' });
          _.map(response.data.data, (item) => {
            _this.sourceItems.push({ name: item.name, value: item.id });
          });
        })
        .catch(function (resp) {
          console.log(resp);
        });
    },
    fetchPaymentMethodList: function () {
      const _this = this;
      axios
        .get("/web/v1/payment-methods")
        .then(function (response) {
          console.log(response);
          // _this.paymentItems.push({ name: _this.$t('common.all'), value: '' });
          _.map(response.data.data, (item) => {
            if (item.parent_id === null) {
              _this.paymentItems.push({ name: item.name, value: item.id });
            }
          });
        })
        .catch(function (resp) {
          console.log(resp);
        });
    },

    fetchOrderTypeList: function () {
      const _this = this;
      axios
        .get("/web/v1/order-types")
        .then(function (response) {
          console.log(response);
          // _this.typeItems.push({ name: _this.$t('common.all'), value: '' });
          _.map(response.data.data, (item) => {
            if (item.parent_id === null) {
              _this.typeItems.push({ name: item.name, value: item.id });
            }
          });
        })
        .catch(function (resp) {
          console.log(resp);
        });
    },

    acceptOrder: function (id) {
      const _this = this;
      var data = {
        order_id: id,
        accepted_datetime: moment().format("YYYY-MM-DD HH:MM"),
      };
      axios
        .post(`/web/v1/orders/accept/${id}`, data)
        .then(function (response) {
          console.log(response);
          _this.showSuccess("Successfully updated the status.");
          _this.reset();
        })
        .catch(function (resp) {
          console.log(resp);
          _this.showError({
            message: "Error : ",
            error: { message: "Something Went Wrong." },
          });
        });
    },

    denyOrder: function (id) {
      const _this = this;
      var data = {
        order_id: id,
        accepted_datetime: moment().format("YYYY-MM-DD HH:MM"),
      };
      axios
        .post(`/web/v1/orders/decline/${id}`, data)
        .then(function (response) {
          console.log(response);
          _this.showSuccess("Successfully updated the status.");
          _this.reset();
        })
        .catch(function (resp) {
          console.log(resp);
          _this.showError({
            message: "Error : ",
            error: { message: "Something Went Wrong." },
          });
        });
    },

    updateOrder: function (id) {
      const _this = this;
      var data = {
        order_id: id,
        requested_time: this.newDate + " " + this.newTime,
      };
      axios
        .patch(`/web/v1/orders/${id}`, data)
        .then(function (response) {
          console.log(response);
          if (response.data.status === "success") {
            _this.showSuccess("Successfully updated.");
            _this.newDate = "";
            _this.newTime = "";
            _this.dialog = false;
            _this.reset();
          } else {
            _this.showError({
              message: "Error : ",
              error: { message: response.data.message },
            });
          }
        })
        .catch(function (resp) {
          console.log(resp);
          _this.showError({
            message: "Error : ",
            error: { message: "Something Went Wrong." },
          });
        });
    },
  },
};
</script>
