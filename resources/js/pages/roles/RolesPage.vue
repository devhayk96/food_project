<template>
  <div class="d-flex flex-column flex-grow-1">
    <div class="d-flex align-center py-3">
      <div>
        <div class="display-1">Roles</div>
        <v-breadcrumbs :items="breadcrumbs" class="pa-0 py-2"></v-breadcrumbs>
      </div>
      <v-spacer></v-spacer>
      <v-btn
        v-if="userCan('roles-create')"
        color="primary"
        :to="{ name: 'roles-create', query: { id: 0 } }"
      >
        Create Role
      </v-btn>
    </div>

    <v-card>
      <v-row dense class="pa-2 align-center">
        <v-col cols="6" class="d-flex text-right align-center">
          <v-text-field
            v-model="searchQuery"
            append-icon="mdi-magnify"
            class="flex-grow-1 mr-md-2"
            solo
            hide-details
            dense
            clearable
            placeholder="e.g. filter for id, email, name, etc"
            @keyup.enter="searchRole(searchQuery)"
          ></v-text-field>
          <v-btn
            :loading="isLoading"
            icon
            small
            class="ml-2"
            @click="initialize()"
          >
            <v-icon>mdi-refresh</v-icon>
          </v-btn>
        </v-col>
      </v-row>

      <v-data-table
        v-model="selectedRoles"
        show-select
        :headers="headers"
        :items="roles"
        :search="searchQuery"
        class="flex-grow-1"
        :footer-props="{
          'items-per-page-options': [10, 20, 50, -1],
        }"
        :items-per-page="itemsPerPage"
      >
        <template v-slot:item.id="{ item }">
          <div class="font-weight-bold">
            #
            <copy-label :text="item.id + ''" />
          </div>
        </template>

        <template v-slot:item.name="{ item }">
          <div class="d-flex align-center py-1">
            <div class="ml-1">
              <copy-label :text="item.name" />
            </div>
          </div>
        </template>

        <template v-slot:item.display_name="{ item }">
          <div class="d-flex align-center py-1">
            <div class="caption font-weight-bold">
              <copy-label :text="item.display_name" />
            </div>
          </div>
        </template>

        <template v-slot:item.description="{ item }">
          <copy-label :text="item.description" />
        </template>

        <template v-slot:item.created_by_user="{ item }">
          <div>
            {{
              item.created_by_user ? "#" + item.created_by_user.id + " " : ""
            }}
            <copy-label
              :text="item.created_by_user ? item.created_by_user.email : ''"
            />
          </div>
        </template>

        <template v-slot:item.updated_by_user="{ item }">
          {{ item.updated_by_user ? "#" + item.updated_by_user.id + " " : "" }}
          <copy-label
            :text="item.updated_by_user ? item.updated_by_user.email : ''"
          />
        </template>

        <template v-slot:item.created_at="{ item }">
          <div>{{ item.created_at | formatDate("ll") }}</div>
        </template>

        <template v-slot:item.updated_at="{ item }">
          <div>{{ item.updated_at | formatDate("ll") }}</div>
        </template>

        <template v-slot:item.action="{ item }">
          <div class="actions d-flex">
            <v-btn
              icon
              v-if="userCan('roles-read')"
              :to="{ name: 'roles-show', query: { id: item.id } }"
            >
              <v-icon>mdi-eye</v-icon>
            </v-btn>
            <v-btn
              icon
              v-if="userCan('roles-update') && item.name != 'superadmin'"
              :to="{ name: 'roles-edit', query: { id: item.id } }"
            >
              <v-icon>mdi-pencil</v-icon>
            </v-btn>
            <v-btn
              icon
              v-if="userCan('roles-delete') && item.name != 'superadmin'"
              @click="deleteRole(item.id)"
            >
              <v-icon>mdi-delete</v-icon>
            </v-btn>
          </div>
        </template>
      </v-data-table>
    </v-card>
    <v-dialog v-model="deleteDialog" max-width="290">
      <v-card>
        <v-card-title class="headline">Delete role</v-card-title>
        <v-card-text>Are you sure you want to delete this role?</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="cancelDeleteRole()">Cancel</v-btn>
          <v-btn color="error" @click="confirmDeleteRole()">Delete</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import { mapActions } from "vuex";
import mixin from "../../helpers/mixin";
import CopyLabel from "../../components/common/CopyLabel";

export default {
  mixins: [mixin],
  components: {
    CopyLabel,
  },
  data() {
    return {
      isLoading: false,
      breadcrumbs: [
        {
          text: "Roles",
          disabled: false,
          href: "#",
        },
        {
          text: "List",
        },
      ],

      searchQuery: "",
      selectedRoles: [],
      headers: [
        { text: "Id", align: "left", value: "id" },
        { text: "Name", align: "left", value: "name" },
        { text: "Display Name", value: "display_name" },
        { text: "Description", value: "description" },
        { text: "Created by", value: "created_by_user" },
        { text: "Updated by", value: "updated_by_user" },
        { text: "Created at", value: "created_at" },
        { text: "Updated at", value: "updated_at" },
        { text: "", sortable: false, align: "right", value: "action" },
      ],

      roles: [],
      deleteDialog: false,
      deletableId: null,
      itemsPerPage: 10,
    };
  },
  watch: {
    selectedRoles(val) {},
  },
  created() {
    this.initialize();
    // set number of lines
    this.itemsPerPage = +this.getNumberOfLines() || 10;
  },
  methods: {
    ...mapActions("app", ["showSuccess", "showError"]),

    initialize() {
      let self = this;
      axios
        .get("/web/v1/roles")
        .then(function (response) {
          self.roles = response.data.roles;
        })
        .catch(function (err) {
          self.showError({ message: "We couldn't get roles.", error: error });
        });
    },
    searchRole() {},
    open() {},
    deleteRole(id) {
      this.deleteDialog = true;
      this.deletableId = id;
    },
    confirmDeleteRole() {
      let self = this;
      axios
        .delete("/web/v1/roles/" + this.deletableId)
        .then(function (response) {
          self.showSuccess("Role has been deleted successfully.");
          setTimeout(() => self.initialize(), 700);
          self.deletableId = null;
        })
        .catch(function (err) {
          self.showError({
            message: "We couldn't delete the role.",
            error: err,
          });
          self.deletableId = null;
        });
      this.deleteDialog = false;
    },
    cancelDeleteRole() {
      this.deletableId = null;
      this.deleteDialog = false;
    },
  },
};
</script>

<style lang="scss" scoped>
.slide-fade-enter-active {
  transition: all 0.3s ease;
}

.slide-fade-leave-active {
  transition: all 0.3s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter,
.slide-fade-leave-to {
  transform: translateX(10px);
  opacity: 0;
}
</style>
