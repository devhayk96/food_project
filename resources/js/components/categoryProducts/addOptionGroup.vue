<template>
  <div v-click-outside="outsideEvent" class="search--add--option--group">
    <v-text-field
      solo
      dense
      :rounded="true"
      @focus="dialog = !dialog"
      label="Search and Add Option Groups"
      class="disable-shadow mr-2"
      v-model.lazy="OptionGroupSearch"
      prepend-inner-icon="mdi-magnify"
    ></v-text-field>
    <v-card v-if="dialog" class="search--option-group-dialog">
      <v-data-table
        :headers="headers"
        :items="
          $store.state.optionGroup.dataList === ''
            ? []
            : $store.state.optionGroup.dataList
        "
        :loading="$store.state.optionGroup.dataList === ''"
        loading-text="fetching data..."
        :show-select="false"
        :single-select="false"
        :items-per-page="-1"
        calculate-widths
        item-key="id"
        :disable-pagination="false"
        :hide-default-footer="true"
        class="page__table"
        :search="OptionGroupSearch"
      >
        <template v-slot:no-data> No Product is added yet! </template>
        <template #item.name="{ item }">
          <div class="flex">
            <img
              class="option--gorup--image--add-product"
              v-bind:src="item.image !== null ? item.image :defaultImg"
              alt=""
            />
            <span>
              {{ item.name }}
            </span>
          </div>
        </template>
        <template #item.add_delete="{ item }">
          <img
            alt="ico"
            @click="addOptionGroup(item.id)"
            class="add-delete--action-button"
            :src="unChecked"
          />
        </template>
      </v-data-table>
    </v-card>
  </div>
</template>

<script>
import { mapActions } from "vuex";
export default {
  name: "addOptionGroup",

  props: {
    checkedSvg: {
      type: String,
      default: require("../../assets/icons/checked.svg"),
    },
    unChecked: {
      type: String,
      default: require("../../assets/icons/unchecked.svg"),
    },
    defaultImg: {
      type: String,
      default: require("../../assets/images/default.jpg"),
    },
    checkedItems: {
      type: Array,
    },
  },

  data: () => ({
    dialog: false,
    OptionGroupSearch: "",
    headers: [
      { text: "Number", value: "number" },
      { text: "Option group name", value: "name" },
      { text: " ", value: "add_delete" },
    ],
  }),

  mounted() {
    this.fetchOptionGroupList();
  },

  methods: {
    ...mapActions("optionGroup", ["fetchOptionGroupList"]),
    ...mapActions("menuCategories", ["addOptionGroups"]),
    outsideEvent() {
      this.dialog = false;
    },

    async addOptionGroup(id) {
      const categoryId = this.$route.query.get;
      const menu_id = this.$store.state.menuCategories.dataDetails.id;
      await this.addOptionGroups({
        menuId: menu_id,
        optionGroupId: id,
        category_id: categoryId,
        addToProducts: this.checkedItems,
      }).then((response) => {
        if (response.status) {
          console.log(response);
          this.$emit("updateData", true);
        }
      });
    },
  },
};
</script>

<style lang="scss" scoped>
.search--add--option--group {
  min-width: 385px;
  position: relative;
}
.search--option-group-dialog {
  top: 45px;
  z-index: 999;
  width: 96.333%;
  max-height: 415px;
  overflow-y: scroll;
  max-height: 415px;
  position: absolute;
}

.search--option-group-dialog::-webkit-scrollbar {
  width: 5px;
}
.search--option-group-dialog::-webkit-scrollbar-track {
  background: #fff;
}
.search--option-group-dialog::-webkit-scrollbar-thumb {
  background-color: #fff;
  border-radius: 20px;
  border: 3px solid #eee;
}

.flex {
  gap: 15px;
  display: flex;
  align-items: center;
}
</style>
