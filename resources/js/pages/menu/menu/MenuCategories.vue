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
          <div class="menu--category--main-content">
            <v-card color="#F2F5F8" outlined class="cureent--menu-information">
              <div>
                <div class="d-flex" v-if="dataDetails.name">
                  <h2>{{ dataDetails.name }}</h2>
                  <span
                    v-if="parseInt(dataDetails.is_blocked) === 1"
                    class="ml-8 fs-13 inactive align-self-end lh-1-8"
                    >Inactive</span
                  >
                  <span class="l-space-8 fs-13 active align-self-end lh-1-8" v-else>Active</span>
                </div>
              </div>
              <span v-if="dataDetails.name" class="fs-13 fw-7 start--end--dates">
                ({{ dataDetails.name }} :
                {{ $moment(dataDetails.start_date).format("LL") }} -
                {{ $moment(dataDetails.end_date).format("LL") }})
              </span>
            </v-card>
            <v-card-title class="categories--card-title">
              <span class="title lh-2-1">Categories</span>
              <v-btn
                dark
                small
                outlined
                height="30"
                color="primary"
                class="back-btn ml-20"
                v-if="userCan('menu_categories-create')"
                @click="$router.push({ name: 'menu-category-manage', query:{ action:'add', id:0 }})"
              >
                <v-icon class="back-btn-arrow"> mdi-plus </v-icon>
                Add Menu Category
              </v-btn>
              <div class="space"></div>
              <v-text-field
                solo
                dense
                label="Search"
                :rounded="true"
                v-model.lazy="search"
                class="disable-shadow"
                @focus="searchIsFocused"
                prepend-inner-icon="mdi-magnify"
              ></v-text-field>
              <v-card-text @mouseover="dr=false">
                <v-data-table
                  :headers="headers"
                  :items="dr?_.filter(fetchedDetails.menu_categories,['is_blocked',0]):selectedCategory"
                  item-key="id"
                  :search="search"
                  class="page__table"
                  :show-select="false"
                  :disable-pagination="true"
                  :hide-default-footer="true"
                  loading-text="fetching data..."
                  :loading="!fetchedDetails.hasOwnProperty('menu_categories')"
                >
                  <template v-slot:no-data>
                    No Category is selected yet!
                  </template>
                  <template v-if="dr" #item.sl_no="{ item, index }">
                    {{index+1}}
                </template>
                <template v-if="dr" #item.name="{ item }">
                    {{item.category.name || '-'}}
                </template>
                  <template v-if="!dr" v-slot:body="props">
                    <draggable @change="saveMenuCategoriesOrder(props.items)" tag="tbody" :list="props.items">
                      <tr v-if="props.items.length === 0">
                        <td colspan="2" class="text--secondary text-center">
                          No Category is selected yet!
                        </td>
                      </tr>
                      <tr
                        v-else
                        v-for="(item, index) in props.items"
                        :key="index"
                      >
                        <td>
                          <img
                            :src="doubleElips"
                            alt=""
                            width="12"
                            class="elipsis"
                        />
                            {{ dr?index + 1:item }}
                        </td>
                        <td>
                            <router-link
                            :to="{ path: '/menu/category/products', query: { get: item } }"
                            @click="() => void 0"
                            class="font-weight-bold"
                            >{{_.find(categoryList, ["id", item]).name || "-"}}
                            </router-link
                        </td>
                        <td class="pl-1">
                          <img
                            v-if="_.find(categoryList, ['id', item]).image"
                            class="menu--category-item-image"
                            alt="Product Image"
                            :src="_.find(categoryList, ['id', item]).image"
                          />
                           <img
                            v-else
                            class="menu--category-item-image"
                            alt="Product Image"
                            :src="defaultImg"
                          />
                        </td>
                        <td>
                           <span
                            v-if="parseInt(_.find(categoryList, ['id', item]).is_blocked) === 1"
                            class="inactive"
                            >Inactive
                           </span>
                           <span class="active" v-else>Active</span>
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
                                    v-for="(i, index) in items"
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
            </v-card-title>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import draggable from "vuedraggable";

export default {
  name: "MenuCategories",

  components: {
    draggable,
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
    defaultImg: {
      type: String,
      default: require("../../../assets/images/default.jpg"),
    },
  },

  async beforeMount() {
    if (this.$route.params.id > 0) {
      this.startCategoryList();
    }
  },

  updated() {
    console.log(this.categoryList);
  },

  created() {
    this.dataDetails.id = this.$route.params.id;
  },

  computed: {
    ...mapGetters({
      fetchedDetails: "menuCategories/getDetails",
      categoryList: "menuCategories/getcategoryList",
      selectedCategory: "menuCategories/getSelectedCategory",
    }),
  },

  data: () => ({
    dr: false,
    search: "",
    headers: [
      { text: "ID", value: "id", align: "start" },
      { text: "Name", value: "name" },
      { text: "Image", value: "img" },
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
    items: [
      {
        title: "Inactive",
        ico: "mdi-circle-off-outline",
        action: "setStatus",
      },
      { title: "Edit", ico: "mdi-pencil", action: "edit" },
      { title: "Delete", ico: "mdi-delete", action: "delete" },
    ],
  }),

  methods: {
    ...mapActions("menuCategories", [
      "saveCategory",
      "fetchDetails",
      "fetchCategoryList",
      "setCategoryStatus",
    ]),

    searchIsFocused() {
      console.log("focused");
    },

    async menuActions(item, action) {
      if (action === "setStatus") {
        await this.setCategoryStatus(item);
      } else if (action === "edit") {
        this.$router.push({
          name: "menu-category-manage",
          query: { action: "edit", id: item },
        });
      }
      this.startCategoryList();
    },

    setStatus(item) {
      const index = this.items.findIndex((i) => i.action == "setStatus");
      if (!Boolean(item.category.is_blocked)) {
        this.items[index].ico = "mdi-circle-outline";
        this.items[index].title = "Active";
      } else {
        this.items[index].ico = "mdi-circle-off-outline";
        this.items[index].title = "Inactive";
      }
    },

    saveMenuCategoriesOrder(data) {
      console.log(data);
      this.saveCategory(data);
    },

    async startCategoryList() {
      await this.fetchCategoryList();
      await this.fetchDetails(this.dataDetails.id);
      console.log(this.selectedCategory);
    },
  },

  watch: {
    fetchedDetails() {
      this.dataDetails = this.fetchedDetails;
    },
  },
};
</script>

<style scoped lang="scss">
a {
  text-decoration: none !important;
}
.space {
  flex-grow: 5 !important;
}

.v-menu__content {
  border-radius: 0;
}

.elipsis {
  margin-right: 20px;
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
</style>
