<template>
  <div class="d-flex flex-grow-1 flex-column">
    <v-row class="flex-grow-0" dense>
      <v-col cols="12">
        <v-card>
          <v-container :fluid="!isContentBoxed">
            <v-card-title class="search-filter mb-lg-3">
              <v-row class="align-center">
                <v-col class="col-12 col-sm-5 col-lg-3">
                  <h3>Products</h3>
                  <v-btn
                    v-if="userCan('products-create')"
                    class="ms-2"
                    dark
                    outlined
                    color="primary"
                    :to="{
                      name: 'product-manage',
                      query: { action: 'add', id: 0 },
                    }"
                  >
                    <v-icon dark> mdi-plus </v-icon>
                    Add Product
                  </v-btn>
                </v-col>
                <v-col class="col-12 col-sm-7 col-lg-5 d-sm-flex">
                  <div class="mt-2 me-3">
                    <h4>{{ $t("orders_accepted.filters") | capitalize }}:</h4>
                  </div>
                  <v-text-field
                    v-model="name"
                    label="Name or Article Numbers"
                    :disabled="dataList === ''"
                    @change="
                      () => {
                        fetchList(1);
                      }
                    "
                  ></v-text-field>
                  <div class="mt-2 ms-sm-4">
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
                <v-col class="col-6 col-sm-6 col-lg-2">
                  <v-select
                    v-model="sortBy"
                    label="Sort By"
                    :items="sortList"
                    @change="
                      () => {
                        fetchList(1);
                      }
                    "
                    :disabled="dataList === ''"
                  ></v-select>
                </v-col>
                <v-col class="col-6 col-sm-6 col-lg-2">
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
              </v-row>
              <!-- <v-text-field
                                v-model="search"
                                append-icon="mdi-magnify"
                                label="Search"
                                single-line
                                hide-details
                            ></v-text-field>-->
            </v-card-title>
            <!--<v-container>
                            <v-row>
                                <v-col cols="2" class="py-0">
                                    <div class="mt-2"><b>Sorting</b></div>
                                </v-col>
                                <v-col cols="2" class="py-0">
                                    <v-select
                                        v-model="sortBy"
                                        label="Sort By"
                                        :items="sortList"
                                        @change="fetchList"
                                        :disabled="dataList === ''"
                                    ></v-select>
                                </v-col>
                                <v-col cols="2" class="py-0">
                                    <v-select
                                        v-model="sortByType"
                                        label="Type"
                                        :items="['desc', 'asc']"
                                        @change="fetchList"
                                        :disabled="dataList === ''"
                                    ></v-select>
                                </v-col>
                            </v-row>
                        </v-container>-->
            <!--<v-card>
                            <v-card-title>Filters</v-card-title>
                            <v-card-text>
                                <v-row>
                                    &lt;!&ndash;<v-col cols="3" class="py-0">
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
                                    </v-col>&ndash;&gt;
                                    <v-col cols="3" class="py-0">
                                        <v-text-field
                                            v-model="name"
                                            label="Name or Article Numbers"
                                            prepend-icon="mdi-calendar"
                                            :disabled="dataList === ''"
                                            @change="fetchList"
                                        ></v-text-field>
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
                        </v-card>-->
            <v-data-table
              :headers="headers"
              :items="dataList === '' ? [] : dataList"
              :search="search"
              :loading="dataList === ''"
              loading-text="Loading... Please wait"
              disable-filtering
              disable-sort
              :item-class="dataItemClass"
              :footer-props="{
                'items-per-page-options': [10, 20, 50, -1],
              }"
              :items-per-page="itemsPerPage"
            >
              <template #item.image="{ item }">
                <img
                  height="40"
                  width="40"
                  :src="item.image"
                  class="rounded-circle"
                />
              </template>
              <template #item.name="{ item }">
                <router-link
                  :to="{
                    name: 'product-manage',
                    query: { action: 'edit', id: item.article_number },
                  }"
                  class="font-weight-bold"
                >
                  {{ item.name }}
                </router-link>
              </template>
              <template #item.status="{ item }">
                <span v-if="parseInt(item.status) === 1" class="active"
                  >In Stock</span
                >
                <span class="inactive" v-else>Out of Stock</span>
              </template>
              <template #item.actions="{ item }">
                <v-menu left offset-y>
                  <template v-slot:activator="{ on, attrs }">
                    <img
                      src="/images/custom/three-dots.svg"
                      alt="three-dots"
                      width="20"
                      class="mr-2 dots-img"
                      v-on="on"
                    />
                  </template>
                  <v-list class="actions-list actions-menu_wrap">
                    <v-list-item
                      @click="changeStatus(item)"
                      class="actions-list_item"
                    >
                      <template>
                        <v-btn v-if="userCan('products-update')" icon>
                          <v-icon
                            v-if="parseInt(item.status)"
                            class="actions-list_icon"
                            >mdi-circle-off-outline</v-icon
                          >
                          <v-icon v-else class="actions-list_icon"
                            >mdi-check-circle-outline</v-icon
                          >
                          <span class="actions-list_text">{{
                            parseInt(item.status) === 1
                              ? "Out of Stock"
                              : "In Stock"
                          }}</span>
                        </v-btn>
                      </template>
                    </v-list-item>
                    <v-list-item
                      :to="{
                        name: 'product-manage',
                        query: { action: 'edit', id: item.article_number },
                      }"
                      class="actions-list_item"
                    >
                      <template>
                        <v-btn v-if="userCan('products-update')" icon>
                          <v-icon class="actions-list_icon">mdi-pencil</v-icon>
                          <span class="actions-list_text">Edit</span>
                        </v-btn>
                      </template>
                    </v-list-item>
                    <v-list-item class="actions-list_item">
                      <template>
                        <v-btn icon>
                          <v-icon class="actions-list_icon"
                            >mdi-delete-outline</v-icon
                          >
                          <span class="actions-list_text">Delete</span>
                        </v-btn>
                      </template>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </template>
            </v-data-table>
          </v-container>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import mixin from "../../helpers/mixin";
import { mapState, mapActions } from "vuex";
export default {
  props: {
    elipsis: {
      type: String,
      default: require("../../assets/icons/elipsis-horizontal.svg"),
    },
  },
  mixins: [mixin],
  components: {},
  data() {
    return {
      page: 1,
      pageCount: 0,
      itemsPerPage: 10,
      date: null,
      name: null,
      search: "",
      sortBy: "name",
      sortByType: "asc",
      sortList: [
        { text: "Date", value: "created_at" },
        { text: "Name", value: "name" },
      ],
      headers: [
        /*{
                    text: 'ID',
                    align: 'start',
                    value: 'id'
                },*/
        { text: "Article Number", value: "article_number" },
        { text: "Name", value: "name" },
        { text: "Image", value: "image" },
        // { text: 'Description2', value: 'description_2' },
        // { text: 'Description3', value: 'description_3 || -' },
        // { text: 'Group', value: 'product_subgroups.product_groups.name'},
        { text: "Subgroup", value: "product_subgroups.name" },
        { text: "Vat", value: "vats.vat_code" },
        { text: "Price", value: "price" },
        // { text: 'Course', value: 'course'},
        // { text: 'EAN', value: 'ean'},
        // { text: 'Main Product', value: 'main_product'},
        // { text: 'Receipt', value: 'is_receipt'},
        // { text: 'kitchen', value: 'is_kitchen'},
        // { text: 'Sticker', value: 'is_sticker'},
        // { text: 'Active', value: 'is_blocked'},
        // { text: 'Various', value: 'is_various'},
        // { text: 'Open Number', value: 'is_open_number'},
        // { text: 'Open Price', value: 'is_open_price'},
        // { text: 'Restock', value: 'restock'},
        { text: "Status", value: "status" },
        {
          text: "Actions",
          align: "center",
          value: "actions",
          sortable: false,
        },
      ],
      dataList: "",
      file: null,
      items: [
        {
          title: "Out of Stock",
          ico: "mdi-circle-off-outline",
          action: "changeStatus",
        },
        { title: "Edit", ico: "mdi-pencil", action: "edit" },
        { title: "Delete", ico: "mdi-delete-outline", action: "delete" },
      ],
    };
  },

  computed: {
    ...mapState("app", ["isContentBoxed"]),
  },

  created() {
    // set number of lines
    this.itemsPerPage = +this.getNumberOfLines() || 10;
  },

  mounted() {
    //query checking
    if (!_.isEmpty(this.$route.query)) this.queryCheck();
    this.fetchList();
  },

  beforeDestroy() {},

  methods: {
    ...mapActions("app", ["showSuccess", "showError"]),
    queryCheck: function () {
      if (this.$route.query.hasOwnProperty("name"))
        this.name = this.$route.query.name;
      /*if(this.$route.query.hasOwnProperty("status"))
                this.filters.status = this.$route.query.status;
            if(this.$route.query.hasOwnProperty("source"))
                this.filters.source = this.$route.query.source;
            if(this.$route.query.hasOwnProperty("payment"))
                this.filters.payment = this.$route.query.payment;
            if(this.$route.query.hasOwnProperty("type"))
                this.filters.type = this.$route.query.type;
            if(this.$route.query.hasOwnProperty("isPos"))
                this.filters.isPos = this.$route.query.isPos;*/
      if (this.$route.query.hasOwnProperty("date"))
        this.date = this.$route.query.date;
      if (this.$route.query.hasOwnProperty("page"))
        this.page = parseInt(this.$route.query.page);
      if (this.$route.query.hasOwnProperty("per_page"))
        this.itemsPerPage = parseInt(this.$route.query.per_page);
      if (this.$route.query.hasOwnProperty("sortBy"))
        this.sortBy = this.$route.query.sortBy;
      if (this.$route.query.hasOwnProperty("sortByType"))
        this.sortByType = this.$route.query.sortByType;
    },
    reset: function () {
      this.itemsPerPage = 10;
      this.page = 1;
      this.sortBy = "name";
      this.sortByType = "asc";
      this.name = "";
      this.date = null;
      this.fetchList();
    },
    fetchList: function (page = this.page) {
      const _this = this;
      this.dataList = "";
      const params = {
        sortBy: this.sortBy,
        sortByType: this.sortByType,
        date: this.date ? this.date : "",
        name: this.name ? this.name : "",
      };
      const queryString = new URLSearchParams(params).toString();

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
      axios
        .get(`/web/v1/product?${queryString}`)
        .then(function (response) {
          console.log(response);
          _this.dataList = response.data.data;
        })
        .catch(function (resp) {
          console.log(resp);
        });
    },

    fileUpload: function () {
      const _this = this;
      console.log(this.file);
      if (this.file !== null) {
        const formData = new FormData();
        formData.append("file", this.file);
        axios
          .post("/web/v1/product/csv/import", formData)
          .then(function (response) {
            _this.file = null;
            console.log(response);
            if (response.data) {
              _this.showSuccess(
                `Total:${response.data.total}, Created:${response.data.created}, Updated:${response.data.updated}, Error:${response.data.error}`
              );
            } else {
              _this.showError({
                message: "Error : ",
                error: { message: "Something Went Wrong." },
              });
            }
          })
          .catch(function (error) {
            _this.file = null;
            console.log(error);
            _this.showError({ message: "Error : ", error: error });
          });
      }
    },

    changeStatus(item) {
      const status = parseInt(item.status) ? 0 : 1;
      const index = this.dataList.indexOf(item);

      axios
        .patch(`/web/v1/product/${item.id}`, { status })
        .then((response) => {
          this.dataList[index].status = status;
          this.showSuccess(response.data ? "Updated" : "Not updated");
        })
        .catch((error) => {
          this.showError({ message: "Error: ", error: error });
        });
    },

    dataItemClass() {
      const c_lass = "data-class";
      return c_lass;
    },
  },
};
</script>
<style>
.search-filter h3,
.search-filter h4 {
  font-weight: 600;
  display: inline-block;
  color: #212121;
}
.search-filter h3 {
  font-size: 18px;
}
.search-filter h4 {
  font-size: 16px;
}
.search-filter .theme--light.v-label {
  color: rgba(0, 0, 0, 0.4);
}
.v-card .v-data-table > .v-data-table__wrapper > table > thead > tr > th {
  font-size: 12px;
}
.v-card .v-data-table > .v-data-table__wrapper > table > tbody > tr > td {
  font-size: 14px;
}
.v-card .v-data-table > .v-data-table__wrapper > table > tbody > tr > td a {
  text-decoration: none;
}
.v-data-table > .v-data-table__wrapper {
  padding: 0 30px;
}
.dots-img {
  cursor: pointer;
}
.actions-list {
  border: 1px solid #eee;
  width: 120px;
  border-radius: 0;
  padding: 0;
  font-size: 12px !important;
}
.actions-list_item {
  border-bottom: 1px solid #eee;
  padding-left: 5px;
}
.actions-list_item:hover {
  background-color: #eeeeee;
}
.actions-list_text {
  font-size: 12px;
}
.actions-list_icon::before {
  font-size: 14px !important;
}
.actions-list_item .v-btn--icon.v-size--default {
  width: auto;
}
.actions-list_item .v-btn:before {
  background-color: unset;
}
.actions-menu_wrap {
  position: relative;
}
@media only screen and (max-width: 1199px) {
  .v-data-table > .v-data-table__wrapper {
    padding: 0;
  }
}
</style>
