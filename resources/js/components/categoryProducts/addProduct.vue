<template>
  <div v-click-outside="outsideEvent" class="search--add-products">
    <v-text-field
      solo
      dense
      :rounded="true"
      @focus="dialog = true"
      label="Search and Add Products"
      v-model.lazy="search"
      class="disable-shadow"
      prepend-inner-icon="mdi-magnify"
    ></v-text-field>
    <v-card v-if="dialog" max-height="415" class="search--product-dialog">
      <v-data-table
        :headers="mangeModalDtHeaders"
        v-model="selectedMenuProducts"
        :items="
          productList === ''
            ? []
            : _.map(productList, (p) => {
                var sp = _.find(selectedMenuProducts, ['product_id', p.id]);
                if (sp !== undefined) {
                  return {
                    product_id: p.id,
                    name: p.name,
                    price: sp.price,
                    image: p.image,
                    categories: sp.categories,
                  };
                }
                return {
                  product_id: p.id,
                  name: p.name,
                  price: p.price,
                  image: p.image,
                  categories: [],
                };
              })
        "
        :loading="productList === ''"
        loading-text="fetching data..."
        :show-select="false"
        :single-select="false"
        :items-per-page="-1"
        calculate-widths
        item-key="product_id"
        :disable-pagination="false"
        :hide-default-footer="true"
        class="page__table"
        :search="search"
      >
        <template v-slot:no-data> No Product is added yet! </template>
        <template #item.article_number="{ item }">
          {{ _.find(productList, ["id", item.product_id]).article_number }}
        </template>
        <template #item.name="{ item }">
          <div class="flex">
            <img
              v-if="item.image != null"
              class="product--image--add-product"
              v-bind:src="item.image"
              alt=""
            />
            <img
              v-else
              class="product--image--add-product"
              :src="defaultImg"
              alt=""
            />
            <span>
              {{ _.find(productList, ["id", item.product_id]).name || "-" }}
            </span>
          </div>
        </template>
        <template #item.price="{ item }">
          {{ item.price }}
        </template>
        <template #item.add_delete="{ item }">
          <img
            alt="ico"
            @click="addDeleteCategoryProduct(item)"
            class="add-delete--action-button"
            :src="unChecked"
          />
        </template>
      </v-data-table>
    </v-card>
  </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";

export default {
  name: "addProduct",

  props: {
    checkedSvg: {
      type: String,
      default: require("../../assets/icons/checked.svg"),
    },
    unChecked: {
      type: String,
      default: require("../../assets/icons/unchecked.svg"),
    },
    selectedItems: {
      type: Array,
    },
    defaultImg:{
    type:String,
    default:require('../../assets/images/default.jpg')
    },
    data: {
      type: String | Object,
    },
    save: Number,
  },

  async created() {
    await this.reAssignMenuProduct();
    this.selectedMenuProducts = this.menuProducts;
    this.selectedMenuProductIds = this.menuProductId;
  },

  computed: {
    ...mapGetters({
      productList: "menuCategories/getProductList",
      menuProducts: "menuCategories/getselectedMenuProducts",
      menuProductId: "menuCategories/getselectedMenuProductIds",
    }),
  },

  updated() {},

  data: () => ({
    search: "",
    dialog: false,
    menuProductsAll:[],
    showItVisually:{},
    selectedMenuProducts: [],
    selectedMenuProductIds: [],
    mangeModalDtHeaders: [
      { text: "Article Number", value: "article_number" },
      { text: "name", value: "name" },
      { text: "price", value: "price" },
      { text: " ", value: "add_delete" },
    ],
  }),

  methods: {
    ...mapActions("menuCategories", [
      "fetchDetails",
      "reAssignMenuProduct",
      "saveMenuProduct",
      "fetchProductList",
    ]),
    ...mapActions("app", ["showSuccess"]),

    outsideEvent() {
      this.dialog = false;
    },

    async addDeleteCategoryProduct(item) {
      let menuProductsAll = this.menuProducts;
      var i = _.findIndex(menuProductsAll, ["product_id", item.product_id]);
      if(menuProductsAll[i] === undefined) {
      	const data = {
          categories: [this.data.category.id],
          price: item.price,
          product_id: item.product_id,
        };
      	menuProductsAll.push(data)
      	await this.saveMenuProduct(menuProductsAll);
        this.$emit("update", true);
        this.showSuccess('Product Successfully Added to the Category')
        return;
      }
      if (
        !menuProductsAll[i].categories.some((a) => a == this.data.category.id)
      ) {
        menuProductsAll[i].categories.push(this.data.category.id);
        await this.saveMenuProduct(menuProductsAll);
        this.$emit("update", true);
        this.showSuccess('Product Successfully Added to the Category')
      } else {
        const index = menuProductsAll[i].categories.indexOf(
          this.data.category.id
        );
        menuProductsAll[i].categories.splice(index, 1);
        console.log(menuProductsAll[i], "removed");
        await this.saveMenuProduct(menuProductsAll);
        this.$emit("update", true);
        this.showSuccess('Product Successfully Removed from the Category')
      }
      await this.fetchDetails(this.$store.state.menuCategories.dataDetails.id);
      await this.reAssignMenuProduct();
    },
  },


  watch: {
    selectedItems() {
      this.selectedItems = this.selectedItems;
    },
    async save() {
      await this.saveMenuProduct(this.menuProductsAll);
      this.$emit("update", true);
      this.showSuccess("Updated");
      await this.fetchDetails(this.$store.state.menuCategories.dataDetails.id);
      await this.reAssignMenuProduct();
      console.log(this.save);
    },
  },
};
</script>

<style lang="scss" scoped>
.search--add-products {
  min-width: 464px;
  max-height: 415px;
  position: relative;
}

.search--product-dialog {
  top: 45px;
  width: 100%;
  z-index: 999;
  max-height: 415px;
  overflow-y: scroll;
  max-height: 415px;
  position: absolute;
}

.search--product-dialog::-webkit-scrollbar {
  width: 5px;
}
.search--product-dialog::-webkit-scrollbar-track {
  background: #fff;
}
.search--product-dialog::-webkit-scrollbar-thumb {
  background-color: #fff;
  border-radius: 20px;
  border: 3px solid #eee;
}

.flex {
  gap: 40px;
  display: flex;
  align-items: center;
}
</style>
