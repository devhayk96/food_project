<template>
  <div class="d-flex flex-grow-1 flex-column">
    <v-row class="flex-grow-0" dense>
      <v-col cols="12">
        <v-card class="menu--categories--card">
          <v-btn
            dark
            large
            outlined
            class="back-btn"
            color="primary"
            @click="$router.go(-1)"
          >
            <v-icon class="back-btn-arrow"> mdi-arrow-left </v-icon>
            Go Back
          </v-btn>
          <div class="menu--category--main-content mt-4">
            <v-card
              color="#F2F5F8"
              outlined
              class="current--category-information d-flex"
            >
              <div v-if="productData !== ''" class="flex--control">
                <div class="category--name--status">
                  <img
                    :src="
                      productData.category.image == null
                        ? defaultImg
                        : productData.category.image
                    "
                    alt=""
                  />
                  <div>
                    <h2>
                      {{ productData.category.name }}
                    </h2>
                    <span
                      v-if="parseInt(productData.category.is_blocked) === 1"
                      class="fs-13 inactive"
                      >Inactive</span
                    >
                    <span class="fs-13 active" v-else>Active</span>
                  </div>
                </div>
                <div class="actions">
                  <v-btn
                    text
                    color="#4C4C4C"
                    v-for="item in topCardActions"
                    :key="item.id"
                    @click="topcardAction($route.query.get, item.action)"
                  >
                    <v-icon class="mr-1" small color="#4C4C4C">{{
                      item.ico
                    }}</v-icon>
                    {{ item.title }}
                  </v-btn>
                </div>
              </div>
            </v-card>
            <v-card-title class="categories--card-title">
              <span class="title lh-2-1">Products</span>
              <div class="space"></div>
              <add-option-group
                :checkedItems="optionGroupsTo"
                @updateData="updateState"
              />
              <add-product
                :data="productData"
                @update="fetchDatas"
                :save="saveStateEvent"
                @visualData="getVisualData"
                :selectedItems="selectedProducts"
              />
            </v-card-title>
            <v-card-text class="menu--category--products">
              <v-data-table
                :headers="productHeaders"
                :items="
                  productData === ''
                    ? []
                    : _.filter(productData.products, ['is_blocked', 0])
                "
                :loading="productData === ''"
                :loading-text="'fetching the details...'"
                :show-select="true"
                :single-select="false"
                v-model="optionGroupsTo"
                item-key="id"
                :disable-pagination="true"
                :hide-default-footer="true"
                :sort-by="['weight']"
                class="page__table"
                ref="formRef"
              >
                <template v-slot:no-data> No Product is added yet! </template>
                <template v-slot:body="props">
                  <draggable
                    :list="props.items"
                    tag="tbody"
                    @change="saveProductOrder(props.items)"
                  >
                    <tr
                      class="position-relative"
                      v-if="props.items.length === 0"
                    >
                      <td colspan="2" class="text--secondary text-center">
                        No Product is added yet!
                      </td>
                    </tr>
                    <tr
                      v-else
                      v-for="(item, index) in props.items"
                      :key="index"
                    >
                      <td class="position-relative">
                        <img
                          :src="doubleElips"
                          alt=""
                          width="12"
                          class="elipsis"
                        />
                        <v-checkbox
                          v-model="optionGroupsTo"
                          :value="item"
                          style="margin: 0px; padding: 0px"
                          hide-details
                        />
                      </td>
                      <td>
                        <div class="d-flex">
                          <span>
                            {{
                              _.find(productList, [
                                "id",
                                item.menu_product.product_id,
                              ]).article_number
                            }}
                          </span>
                        </div>
                      </td>
                      <td>
                        <div class="product--name--optional-groups">
                          <span>{{
                            _.find(productList, [
                              "id",
                              item.menu_product.product_id,
                            ]).name || "-"
                          }}</span>
                          <div
                            class="product--option-groups"
                            :class="`group-${item.menu_product.product_id}`"
                          >
                            <span
                              v-for="item in item.menu_product.optional_groups"
                              :key="item.id"
                            >
                              <span
                                v-for="i in item.optional_group"
                                :key="i.id"
                              >
                                <router-link
                                  :to="{
                                    name: 'optional-group-view',
                                    query: { id: item.optional_group_id },
                                  }"
                                >
                                  {{ i.name }}
                                </router-link>
                              </span>
                              <span
                                @click="
                                  removeOptionGroup({
                                    product_id: item.product_id,
                                    menu_category_id: item.menu_category_id,
                                    optional_group_id: item.optional_group_id,
                                    menu_id:
                                      $store.state.menuCategories.dataDetails
                                        .id,
                                  })
                                "
                                class="remove--ico"
                              >
                                <v-icon small> mdi-delete </v-icon>
                              </span>
                            </span>
                          </div>
                        </div>
                      </td>
                      <td>
                        <img
                          v-if="
                            _.find(productList, [
                              'id',
                              item.menu_product.product_id,
                            ]).image
                          "
                          class="product--image"
                          :src="
                            _.find(productList, [
                              'id',
                              item.menu_product.product_id,
                            ]).image
                          "
                          alt=""
                        />
                        <img
                          v-else
                          class="product--image"
                          :src="defaultImg"
                          alt=""
                        />
                      </td>
                      <td class="text-center">
                        <product-price-mod :product="item" />
                      </td>
                      <td>
                        <div
                          @click="
                            showOptionGroups(
                              `group-${item.menu_product.product_id}`
                            )
                          "
                          class="option--gorups-hide-show--action"
                        >
                          <span>Option Groups</span>
                          <img :src="arrowDown" />
                        </div>
                      </td>
                      <td>
                        <span
                          v-if="parseInt(item.is_blocked) === 1"
                          class="inactive"
                          >Out of Stock</span
                        >
                        <span class="active" v-else>In Stock</span>
                      </td>
                      <td>
                        <div class="text-center">
                          <v-menu offset-y left class="mini-menu-custom">
                            <template v-slot:activator="{ on, attrs }">
                              <img
                                :src="elipsis"
                                alt=""
                                width="30"
                                v-on="on"
                                v-bind="attrs"
                                class="elipsis"
                              />
                            </template>
                            <v-list dense class="p-off">
                              <v-list-item-group>
                                <v-list-item
                                  class="list-item"
                                  v-for="(i, index) in actions"
                                  :key="index"
                                  @click="menuActions(item, i.action)"
                                >
                                  <v-list-item-title>
                                    <v-icon small class="mr-1">{{
                                      i.ico
                                    }}</v-icon>
                                    {{ i.title }}
                                  </v-list-item-title>
                                </v-list-item>
                              </v-list-item-group>
                            </v-list>
                          </v-menu>
                        </div>
                      </td>
                    </tr>
                  </draggable>
                </template>
              </v-data-table>
            </v-card-text>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import draggable from "vuedraggable";
import { mapGetters, mapActions } from "vuex";
import addProduct from "@/components/categoryProducts/addProduct";
import addOptionGroup from "@/components/categoryProducts/addOptionGroup";
import productPriceMod from "@/components/categoryProducts/productPriceMod";

export default {
  name: "MenuCategoryProducts",

  components: {
    draggable,
    addProduct,
    addOptionGroup,
    productPriceMod,
  },

  updated() {
    console.log(this.productData);
  },

  props: {
    elipsis: {
      type: String,
      default: require("../../../assets/icons/elipsis-horizontal.svg"),
    },
    doubleElips: {
      type: String,
      default: require("../../../assets/icons/double-elips-line.svg"),
    },

    arrowDown: {
      type: String,
      default: require("../../../assets/icons/arrow-down.svg"),
    },

    arrowUp: {
      type: String,
      default: require("../../../assets/icons/arrow-up.svg"),
    },
    defaultImg: {
      type: String,
      default: require("../../../assets/images/default.jpg"),
    },
  },

  async beforeMount() {
    if (this.$route.query.get > 0) {
      await this.fetchDatas();
      this.selectedProducts = this.$refs.formRef.selectableItems;
    }
  },

  computed: {
    ...mapGetters({
      fetchedDetails: "menuCategories/getDetails",
      productList: "menuCategories/getProductList",
      categoryList: "menuCategories/getcategoryList",
      selectedCategory: "menuCategories/getSelectedCategory",
    }),
  },

  data: () => ({
    productData: "",
    topCardActions: [
      {
        id: 1,
        title: "Inactive",
        ico: "mdi-circle-off-outline",
        action: "setStatus",
      },
      { id: 2, title: "Edit", ico: "mdi-pencil", action: "edit" },
      { id: 3, title: "Delete", ico: "mdi-delete-outline", action: "delete" },
    ],
    productHeaders: [
      { text: "Article Number", value: "article_number" },
      { text: "Name", value: "name" },
      { text: "Image", value: "Image", align: "start" },
      { text: "Price", value: "price", align: "center" },
      { text: "Option Group", value: "option_group", align: "center" },
      { text: "Status", value: "status" },
      { text: "Action", value: "action", align: "center" },
    ],
    dataDetails: {
      id: 0,
      name: "",
      description: "",
      is_blocked: false,
      start_date: null,
      end_date: null,
    },

    actions: [
      {
        title: "Inactive",
        ico: "mdi-circle-off-outline",
        action: "setStatus",
      },
      { title: "Edit", ico: "mdi-pencil", action: "edit" },
      { title: "Delete", ico: "mdi-delete", action: "delete" },
    ],
    selectedProducts: [],
    optionGroupsTo: [],
    visibleOptionGroups: false,
    saveStateEvent: 0,
    optionGroupsFetched: [],
  }),

  methods: {
    ...mapActions("menuCategories", [
      "fetchDetails",
      "fetchProductList",
      "fetchCategoryList",
      "setCategoryStatus",
      "reAssignMenuProduct",
      "saveMenuProductOrder",
      "removeCategoryProduct",
      "setCategoryProductStatus",
      "removeProductOptionalGroup",
    ]),

    async topcardAction(id, action) {
      if (action === "setStatus") {
        await this.setCategoryStatus(id);
        await this.updateState();
        this.$router.go(-1);
      } else if (action === "edit") {
        this.$router.push({
          name: "menu-category-manage",
          query: { action: "edit", id: id },
        });
      }
    },

    async menuActions(item, action) {
      if (action === "edit") {
        this.$router.push({
          name: "product-manage",
          query: { action: "edit", id: item.id },
        });
      }
      if (action === "delete" || action === "setStatus") {
        if (action === "delete") {
          await this.removeCategoryProduct({
            menu_product_id: item.menu_product_id,
            menu_category_id: item.menu_category_id,
          });
        } else if (action === "setStatus") {
          await this.setCategoryProductStatus({
            menu_product_id: item.menu_product_id,
            menu_category_id: item.menu_category_id,
          });
        }
        await this.fetchDatas();
      }
    },

    async removeOptionGroup(data) {
      await this.removeProductOptionalGroup(data);
      this.fetchDatas();
    },

    async updateState(value) {
      console.log(this.optionGroupsTo)
      let fetchedFor = [];
      this.optionGroupsTo.forEach(item => {
          fetchedFor.push(item.menu_product.product_id)
      })
      await this.fetchDatas();
      this.optionGroupsFetched = fetchedFor;
      for(let i = 0; i < this.optionGroupsFetched.length; i++) {
          document.querySelector(`.group-${this.optionGroupsFetched[i]}`).style.height = "20px";
      }
    },

    showOptionGroups(x) {
      this.visibleOptionGroups = !this.visibleOptionGroups;
      if (this.visibleOptionGroups) {
        document.querySelector(`.${x}`).style.height = "20px";
      } else {
        document.querySelector(`.${x}`).style.height = "0";
      }
    },

    async saveProductOrder(data) {
      await this.saveMenuProductOrder(data);
    },

    findCategoryData() {
      let categories = _.filter(
        this.$store.state.menuCategories.dataDetails.menu_categories,
        ["is_blocked", 0]
      );
      const category = categories.filter(
        (i) => i.menu_category_id == this.$route.query.get
      );
      this.productData = category[0];
      this.optionGroupsTo = [];
    },

    getVisualData(value) {
      let product = _.filter(this.productList, ["id", value]);
    },

    async fetchDatas() {
      await this.fetchDetails(this.$store.state.menuCategories.dataDetails.id);
      await this.fetchProductList();
      await this.fetchCategoryList();
      await this.findCategoryData();
      this.selectedProducts = this.$refs.formRef.selectableItems;
    },

    saveState() {
      this.saveStateEvent = Math.random();
    },
  },
};
</script>

<style lang="scss" scoped>
.space {
  flex: 5;
}

.menuable__content__active::before {
  content: " ";
  height: 30px;
  width: 30px;
  top: -10px;
  left: 68px;
  z-index: -1;
  background: #fff;
  position: absolute;
  border: 1px solid #eee;
  transform: rotate(45deg);
}

.v-menu__content {
  width: 115px;
  margin-top: 8px;
  margin-left: 17px;
  overflow-y: auto;
  contain: none !important;
  overflow-x: unset !important;
  overflow-y: unset !important;
}
.remove--ico {
  cursor: pointer;
}

.save-btn {
  width: 160px;
  height: 42px !important;
  font: normal normal 600 14px/18px Quicksand;
  background: #0096c7 !important;
  color: #ffffff !important;
  letter-spacing: 1.4px;
}

.product--option-groups {
  height: 0;
  transition: 0.5s;
}

.show {
  height: 20px !important;
}
</style>
