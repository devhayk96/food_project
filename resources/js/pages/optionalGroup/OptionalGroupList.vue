<template>
  <div class="d-flex flex-grow-1 flex-column optional-group-list">
    <v-row class="flex-grow-0" dense>
      <v-col cols="12">
        <v-card>
          <v-card-title class="pa-0">
            <div class="row-wrap">
              <v-row class="ma-3">
                <v-btn
                  v-if="userCan('optional_groups-create')"
                  dark
                  outlined
                  color="primary"
                  :to="{
                    name: 'optional-group-manage',
                    query: { action: 'add', id: 0 },
                  }"
                >
                  <v-icon dark> mdi-plus </v-icon>
                  Add Option Group
                </v-btn>
              </v-row>
              <v-row
                class="
                  d-flex
                  align-center
                  justify-between
                  mt-6
                  mr-11
                  mb-2
                  ml-11
                "
              >
                Option Group List
                <v-spacer></v-spacer>
                <v-col lg="3" sm="12" class="pa-0">
                  <v-text-field
                    v-model="search"
                    prepend-inner-icon="mdi-magnify"
                    label="Search"
                    single-line
                    hide-details
                    outlined
                    dense
                    class="search-input"
                  ></v-text-field>
                </v-col>
              </v-row>
            </div>
          </v-card-title>
          <div class="pl-10 pr-10">
            <v-data-table
              :headers="headers"
              :items="
                $store.state.optionGroup.dataList === ''
                  ? []
                  : $store.state.optionGroup.dataList
              "
              :search="search"
              :loading="$store.state.optionGroup.dataList === ''"
              loading-text="Loading... Please wait"
              :footer-props="{
                'items-per-page-options': [10, 20, 50, -1],
              }"
              :items-per-page="itemsPerPage"
            >
              <template #item.name="{ item }">
                <router-link
                  class="link"
                  :to="{ name: 'optional-group-view', query: { id: item.id } }"
                >
                  {{ item.name }}
                </router-link>
              </template>
              <template #item.image="{ item }">
                <v-img class="table-img" :src="item.image"></v-img>
                <!--                           <v-img :src='item.image' alt="cat-img" />-->
              </template>
              <template #item.is_active="{ item }">
                <span
                  :class="
                    parseInt(item.is_active) === 1
                      ? 'text-active'
                      : 'text-inactive'
                  "
                >
                  {{ parseInt(item.is_active) === 1 ? "Active" : "Inactive" }}
                </span>
              </template>
              <template #item.no_discount="{ item }">
                <span>
                  {{ parseInt(item.no_discount) === 1 ? "No" : "Yes" }}
                </span>
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
                    <v-list-item class="actions-list_item">
                      <template>
                        <v-btn
                          v-if="userCan('optional_groups-update')"
                          icon
                          @click="changeStatus(item)"
                        >
                          <v-icon
                            v-if="parseInt(item.is_active)"
                            class="actions-list_icon"
                            >mdi-circle-off-outline</v-icon
                          >
                          <v-icon v-else class="actions-list_icon"
                            >mdi-check-circle-outline</v-icon
                          >
                          <span class="actions-list_text">{{
                            parseInt(item.is_active) ? "Inactive" : "Active"
                          }}</span>
                        </v-btn>
                      </template>
                    </v-list-item>
                    <v-list-item class="actions-list_item">
                      <template>
                        <v-btn
                          v-if="userCan('optional_groups-update')"
                          icon
                          :to="{
                            name: 'optional-group-manage',
                            query: { action: 'edit', id: item.id },
                          }"
                        >
                          <v-icon class="actions-list_icon">mdi-pencil</v-icon>
                          <span class="actions-list_text">Edit</span>
                        </v-btn>
                      </template>
                    </v-list-item>
                    <v-list-item class="actions-list_item">
                      <template>
                        <v-btn
                          v-if="userCan('optional_groups-delete')"
                          icon
                          @click="
                            deleteOptionGroupId = item.id;
                            deleteDialog = true;
                          "
                          v-bind="item"
                        >
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
          </div>
        </v-card>
      </v-col>
    </v-row>

    <v-dialog v-model="deleteDialog" persistent max-width="400">
      <v-card>
        <v-card-text class="text-h5">{{ deleteDialogTitle }}</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="primary darken-1"
            text
            @click="
              deleteDialog = false;
              deleteOptionGroupId = null;
            "
          >
            Cancel
          </v-btn>
          <v-btn color="red darken-1" text @click="deleteOptionGroup()">
            Delete
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import { mapActions } from "vuex";
import mixin from "../../helpers/mixin";
export default {
  name: "OptionalGroupList",
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
      itemsPerPage: 10,
      deleteDialog: false,
      deleteDialogTitle: "Are you sure you want to delete this option group?",
      deleteOptionGroupId: null,
      headers: [
        {
          text: "ID",
          align: "start",
          value: "id",
        },
        { text: "Number", value: "number" },
        { text: "Name", value: "name" },
        { text: "Image", value: "image" },
        { text: "Description", value: "description" },
        { text: "Status", value: "is_active" },
        { text: "Discount", value: "no_discount" },
        { text: "Type", value: "type" },
        {
          text: "Actions",
          align: "end",
          value: "actions",
          sortable: false,
        },
      ],
    };
  },

  mounted() {
    this.fetchOptionGroupList();
    // set number of lines
    this.itemsPerPage = +this.getNumberOfLines() || 10;
  },

  beforeDestroy() {},

  methods: {
    ...mapActions("app", ["showSuccess", "showError"]),
    ...mapActions("optionGroup", ["fetchOptionGroupList"]),
    deleteOptionGroup() {
      axios
        .delete(`/web/v1/optional-group/${this.deleteOptionGroupId}`)
        .then((response) => {
          if (response.data.message) {
            let optionGroupIndex =
              this.$store.state.optionGroup.dataList.findIndex(
                (item, index) => {
                  if (item.id === this.deleteOptionGroupId) {
                    return true;
                  }
                }
              );
            this.$store.state.optionGroup.dataList.splice(optionGroupIndex, 1);

            this.showSuccess(response.data.message);
          } else {
            this.showError({
              message: "Error : ",
              error: { message: "Something Went Wrong." },
            });
          }

          this.deleteDialog = false;
          this.deleteOptionGroupId = null;
        })
        .catch((error) => {
          this.deleteDialog = false;
          this.deleteOptionGroupId = null;
          console.log(error);
          this.showError({ message: "Error: ", error: error });
        });
    },
    changeStatus(item) {
      const is_active = parseInt(item.is_active) ? 0 : 1;
      const index = this.$store.state.optionGroup.dataList.indexOf(item);

      axios
        .patch(`/web/v1/optional-group/${item.id}`, { is_active })
        .then((response) => {
          this.$store.state.optionGroup.dataList[index].is_active = is_active;
          this.showSuccess(response.data.success ? "Updated" : "Not updated");
        })
        .catch((error) => {
          console.log(error);
          this.showError({ message: "Error: ", error: error });
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.search-input {
  border: 1px solid #eee;
  border-radius: 100px;
}
.optional-group-list {
  .row-wrap {
    width: 100%;
  }

  .dots-img {
    cursor: pointer;
  }
}
.actions-list {
  border: 1px solid #eee;
  width: 120px;
  border-radius: 0;
  padding: 0;
  font-size: 12px !important;

  &_item {
    border-bottom: 1px solid #eee;
    padding-left: 5px;

    &:hover {
      background-color: #eeeeee;
    }
  }

  &_text {
    font-size: 12px;
  }

  &_icon {
    &:before {
      font-size: 14px !important;
    }
  }
}
.table-img {
  height: 60px;
  width: 60px;
  object-fit: cover;
  border-radius: 50%;
}

.text {
  &-active {
    color: #3eb53e;
  }
  &-inactive {
    color: #eb4843;
  }
}
.link {
  color: #0096c7;
  text-decoration: none;
  text-transform: capitalize;
}
</style>

<style lang="scss">
.optional-group-list {
  .theme--light.v-data-table
    > .v-data-table__wrapper
    > table
    > thead
    > tr
    > th {
    color: #757575;
  }

  .v-data-table table tbody tr {
    height: 100px;
  }

  .theme--light.v-data-table
    > .v-data-table__wrapper
    > table
    > tbody
    > tr:not(:last-child)
    > td:not(.v-data-table__mobile-row),
  .theme--light.v-data-table
    > .v-data-table__wrapper
    > table
    > tbody
    > tr:not(:last-child)
    > th:not(.v-data-table__mobile-row) {
    border-bottom: 1px solid #eee !important;
  }
}
.v-menu__content {
  border-radius: 0;
}
.actions-list_item {
  .v-btn--icon.v-size--default {
    width: auto;
  }
  .v-btn:before {
    background-color: unset;
  }
}
.actions-menu_wrap {
  position: relative;
}
</style>
