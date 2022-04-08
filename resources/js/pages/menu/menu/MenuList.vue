<template>
  <div class="d-flex flex-grow-1 flex-column">
    <v-row class="flex-grow-0" dense>
      <v-col cols="12">
        <v-card class="pt-16">
          <v-btn
            v-if="userCan('menus-create')"
            class="mx-2"
            dark
            outlined
            color="primary"
            :to="{ name: 'menu-manage', query: { action: 'add', id: 0 } }"
          >
            <v-icon dark> mdi-plus </v-icon>
            Add Menu
          </v-btn>
          <div class="p-list">
            <v-card-title>
              Menu List
              <div class="space"></div>
              <v-text-field
                v-model="search"
                solo
                dense
                :rounded="true"
                class="disable-shadow"
                prepend-inner-icon="mdi-magnify"
                label="Search"
                :item-class="dataItemClass"
              ></v-text-field>
            </v-card-title>
            <v-data-table
              :headers="headers"
              :items="dataList === '' ? [] : dataList"
              :search="search"
              class="menu-list-data"
              :loading="dataList === ''"
              :item-class="dataItemClass"
              loading-text="Loading... Please wait"
              :footer-props="{
                'items-per-page-options': [10, 20, 50, -1],
              }"
              :items-per-page="itemsPerPage"
            >
              <template #item.image="{ item }">
                <v-img
                  max-height="60"
                  max-width="100"
                  :src="'../../' + item.image == '' ? defaultImg : item.image"
                ></v-img>
                <!--                           <img :src='item.image' alt="cat-img" />-->
              </template>
              <template #item.id="{ item, index }">
                {{ index + 1 }}
              </template>
              <template #item.name="{ item }">
                <router-link
                  :to="{ name: 'menu-categories', params: { id: item.id } }"
                  @click="() => void 0"
                  class="font-weight-bold"
                  >{{ item.name }}</router-link
                >
              </template>
              <template #item.is_blocked="{ item }">
                <span v-if="parseInt(item.is_blocked) === 1" class="inactive"
                  >Inactive</span
                >
                <span class="active" v-else>Active</span>
              </template>

              <template #item.actions="{ item }" class="position-relative">
                <div class="text-center">
                  <v-menu offset-y left class="mini-menu-custom">
                    <template v-slot:activator="{ on, attrs }">
                      <img
                        :src="elipsis"
                        alt=""
                        width="30"
                        v-on="on"
                        v-bind="attrs"
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
                            <v-icon small class="mr-1">{{ i.ico }}</v-icon>
                            {{ i.title }}
                          </v-list-item-title>
                        </v-list-item>
                      </v-list-item-group>
                    </v-list>
                  </v-menu>
                </div>
              </template>
            </v-data-table>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>


<script>
import mixin from "../../../helpers/mixin";
import { mapActions, mapGetters } from "vuex";

export default {
  name: "MenuList",
  mixins: [mixin],
  props: {
    name: {
      type: String,
      default: "",
    },
    elipsis: {
      type: String,
      default: require("../../../assets/icons/elipsis-horizontal.svg"),
    },
    defaultImg: {
      type: String,
      default: require("../../../assets/images/default.jpg"),
    },
  },

  computed: {
    ...mapGetters({
      dataList: "menuList/getMenuList",
    }),
  },

  data() {
    return {
      search: "",
      itemsPerPage: 10,
      headers: [
        {
          text: "ID",
          align: "start",
          value: "id",
        },
        { text: "Name", value: "name" },
        { text: "Description", value: "description" },
        { text: "Start Date", value: "start_date" },
        { text: "End Date", value: "end_date" },
        { text: "Status", value: "is_blocked" },
        {
          text: "Actions",
          align: "end",
          value: "actions",
          sortable: false,
        },
      ],
      items: [
        {
          title: "Inactive",
          ico: "mdi-circle-off-outline",
          action: "setStatus",
        },
        { title: "Edit", ico: "mdi-pencil", action: "edit" },
        { title: "Delete", ico: "mdi-delete", action: "delete" },
      ],
    };
  },

  mounted() {
    this.fetchList();
    // set number of lines
    this.itemsPerPage = +this.getNumberOfLines() || 10;
  },

  created() {},

  beforeDestroy() {},

  methods: {
    ...mapActions("menuList", [
      "fetchMenuList",
      "setMenuListItemStatus",
      "deleteMenuListItem",
    ]),

    fetchList: async function () {
      await this.fetchMenuList();
    },

    async menuActions(item, action) {
      if (action === "edit") {
        this.$router.push({
          name: "menu-details",
          query: { action: "details", id: item.id },
        });
      } else if (action === "setStatus") {
        await this.setMenuListItemStatus(item);
        this.setStatus(item);
      } else if (action === "delete") {
        await this.deleteMenuListItem(item.id);
      }
    },

    setStatus(item) {
      const index = this.items.findIndex((i) => i.action == "setStatus");
      if (!Boolean(item.is_blocked)) {
        this.items[index].ico = "mdi-circle-outline";
        this.items[index].title = "Active";
      } else {
        this.items[index].ico = "mdi-circle-off-outline";
        this.items[index].title = "Inactive";
      }
    },

    dataItemClass(item) {
      const c_lass = "data-class";
      return c_lass;
    },
  },

  watch: {},
};
</script>

<style scoped type="scss">
.pt-16 {
  padding-top: 16px !important;
}

.space {
  flex-grow: 5 !important;
}

a {
  text-decoration: none !important;
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

.list-item {
  border-bottom: 1px solid #eee;
}

.v-text-field--rounded > .v-input__control > .v-input__slot {
  border: 1px solid #eee !important;
}
</style>
