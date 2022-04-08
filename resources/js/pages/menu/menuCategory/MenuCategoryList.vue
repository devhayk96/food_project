<template>
  <div class="d-flex flex-grow-1 flex-column">
    <v-row class="flex-grow-0" dense>
      <v-col cols="12">
        <v-card>
          <v-card-title>
            Menu Category List
            <v-btn
              v-if="userCan('menu_categories-create')"
              class="mx-2"
              dark
              outlined
              color="primary"
              :to="{
                name: 'menu-category-manage',
                query: { action: 'add', id: 0 },
              }"
            >
              <v-icon dark> mdi-plus </v-icon>
              Add Menu Category
            </v-btn>
            <v-spacer></v-spacer>
            <v-text-field
              v-model="search"
              append-icon="mdi-magnify"
              label="Search"
              single-line
              hide-details
            ></v-text-field>
          </v-card-title>
          <v-data-table
            :headers="headers"
            :items="dataList === '' ? [] : dataList"
            :search="search"
            :loading="dataList === ''"
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
                :src="'../../' + item.image"
              ></v-img>
              <!--                           <img :src='item.image' alt="cat-img" />-->
            </template>
            <template #item.is_blocked="{ item }">
              <v-icon v-if="parseInt(item.is_blocked) === 1" class="red--text"
                >mdi-close</v-icon
              >
              <v-icon v-else>mdi-check</v-icon>
            </template>
            <template #item.actions="{ item }">
              <v-btn
                v-if="userCan('menu_categories-read')"
                icon
                class="text-center"
                :to="{
                  name: 'menu-category-manage',
                  query: { action: 'edit', id: item.id },
                }"
              >
                <v-icon>mdi-open-in-new</v-icon>
              </v-btn>
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import mixin from "../../../helpers/mixin";

export default {
  name: "MenuCategoryList",
  mixins: [mixin],
  props: {
    name: {
      type: String,
      default: "",
    },
  },
  components: {},
  data() {
    return {
      search: "",
      headers: [
        {
          text: "ID",
          align: "start",
          value: "id",
        },
        { text: "Name", value: "name" },
        { text: "Image", value: "image" },
        { text: "Description", value: "description" },
        { text: "Active", value: "is_blocked" },
        { text: "Food", value: "is_food" },
        { text: "Drink", value: "is_drink" },
        {
          text: "Actions",
          align: "end",
          value: "actions",
          sortable: false,
        },
      ],
      dataList: "",
      itemsPerPage: 10,
    };
  },

  mounted() {
    this.fetchList();
    // set number of lines
    this.itemsPerPage = +this.getNumberOfLines() || 10;
  },

  beforeDestroy() {},

  methods: {
    fetchList: function () {
      const _this = this;
      axios
        .get("/web/v1/menu-category")
        .then(function (response) {
          console.log(response);
          _this.dataList = response.data.data;
        })
        .catch(function (resp) {
          console.log(resp);
        });
    },
  },
};
</script>
