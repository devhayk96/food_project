<template>
  <div class="d-flex flex-column flex-grow-1">
    <div class="d-flex align-center py-3">
      <div>
        <div class="display-1">Users</div>
        <v-breadcrumbs :items="breadcrumbs" class="pa-0 py-2"></v-breadcrumbs>
      </div>
      <v-spacer></v-spacer>
      <v-btn
        v-if="userCan('users-create')"
        color="primary"
        :to="{ name: 'users-create', query: { id: 0 } }"
      >
        Create User
      </v-btn>
    </div>

    <v-card>
      <!-- users list -->
      <v-row dense class="pa-2 align-center">
        <v-col cols="6">
          <v-menu offset-y left>
            <template v-slot:activator="{ on }">
              <transition name="slide-fade" mode="out-in">
                <v-btn v-show="selectedUsers.length > 0" v-on="on">
                  Actions
                  <v-icon right>mdi-menu-down</v-icon>
                </v-btn>
              </transition>
            </template>
            <v-list dense>
              <v-list-item @click>
                <v-list-item-title>Verify</v-list-item-title>
              </v-list-item>
              <v-list-item @click>
                <v-list-item-title>Disable</v-list-item-title>
              </v-list-item>
              <v-divider></v-divider>
              <v-list-item @click>
                <v-list-item-title>Delete</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </v-col>
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
            @keyup.enter="searchUser(searchQuery)"
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
        v-model="selectedUsers"
        show-select
        :headers="headers"
        :items="users"
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
            <v-avatar size="32" class="elevation-1 grey lighten-3">
              <v-img :src="item.avatar" />
            </v-avatar>
            <div class="ml-1">
              <copy-label :text="item.name" />
            </div>
          </div>
        </template>

        <template v-slot:item.email="{ item }">
          <div class="d-flex align-center py-1">
            <div class="caption font-weight-bold">
              <copy-label :text="item.email" />
            </div>
          </div>
        </template>

        <template v-slot:item.phone="{ item }">
          <copy-label :text="item.phone" />
        </template>

        <template v-slot:item.country="{ item }">
          <div>{{ item.country ? item.country.name : "" }}</div>
        </template>

        <template v-slot:item.city="{ item }">
          <div>{{ item.city | capitalize }}</div>
        </template>

        <template v-slot:item.attached_roles="{ item }">
          <v-chip
            label
            small
            class="font-weight-bold"
            :color="
              item.attached_roles in roleColors
                ? roleColors[item.attached_roles]
                : ''
            "
            >{{ item.attached_roles | capitalize }}
          </v-chip>
        </template>

        <template v-slot:item.status="{ item }">
          <v-chip
            label
            small
            class="font-weight-bold"
            :color="
              item.status in statusColors ? statusColors[item.status] : ''
            "
            >{{ allStatuses[item.status] }}
          </v-chip>
        </template>

        <template v-slot:item.last_login_ip="{ item }">
          <div>
            <copy-label :text="item.last_login_ip" />
          </div>
        </template>

        <template v-slot:item.last_login_at="{ item }">
          <!-- <div>{{ item.last_login_at | formatDate('lll') }}</div> -->
          <div>{{ item.last_login_at }}</div>
        </template>

        <template v-slot:item.created_by="{ item }">
          <div>
            {{
              item.created_by_user ? "#" + item.created_by_user.id + " " : ""
            }}
            <copy-label
              :text="item.created_by_user ? item.created_by_user.email : ''"
            />
          </div>
        </template>

        <template v-slot:item.updated_by="{ item }">
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
              v-if="userCan('users-read')"
              :to="{ name: 'users-show', query: { id: item.id } }"
            >
              <v-icon>mdi-eye</v-icon>
            </v-btn>
            <v-btn
              icon
              v-if="
                userCan('users-update') &&
                (hasRole('superadmin') ||
                  !item.attached_roles.includes('superadmin'))
              "
              :to="{ name: 'users-edit', query: { id: item.id } }"
            >
              <v-icon>mdi-pencil</v-icon>
            </v-btn>
            <v-btn
              icon
              v-if="
                userCan('users-delete') &&
                (hasRole('superadmin') ||
                  !item.attached_roles.includes('superadmin'))
              "
              @click="deleteUser(item.id)"
            >
              <v-icon>mdi-delete</v-icon>
            </v-btn>
          </div>
        </template>
      </v-data-table>
    </v-card>
    <v-dialog v-model="deleteDialog" max-width="290">
      <v-card>
        <v-card-title class="headline"
          >Delete {{ deleteUserHeader }}</v-card-title
        >
        <v-card-text
          >Are you sure you want to delete {{ deleteUserText }}?</v-card-text
        >
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="cancelDeleteUser()">Cancel</v-btn>
          <v-btn color="error" @click="confirmDeleteUser()">Delete</v-btn>
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
          text: "Users",
          disabled: false,
          href: "#",
        },
        {
          text: "List",
        },
      ],

      searchQuery: "",
      selectedUsers: [],
      headers: [
        { text: "Id", align: "left", value: "id" },
        { text: "Name", align: "left", value: "name" },
        { text: "Email", value: "email" },
        { text: "Phone", value: "phone" },
        { text: "Country", value: "country.name" },
        { text: "City", value: "city" },
        { text: "Role", value: "attached_roles" },
        { text: "Status", value: "status" },
        { text: "Last login ip", value: "last_login_ip" },
        { text: "Last login at", value: "last_login_at" },
        { text: "Created by", value: "created_by" },
        { text: "Updated by", value: "updated_by" },
        { text: "Created at", value: "created_at" },
        { text: "Updated at", value: "updated_at" },
        { text: "", sortable: false, align: "right", value: "action" },
      ],

      users: [],
      allStatuses: [],
      roleColors: {
        superadmin: "success",
        admin: "info",
        manager: "primary",
        operations: "secondary",
      },
      statusColors: {
        1: "success",
        2: "warning",
      },
      deleteDialog: false,
      deletableId: null,
      deleteUserHeader: "User",
      deleteUserText: "this user",
      itemsPerPage: 10,
    };
  },
  watch: {
    selectedUsers(val) {},
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
        .get("/web/v1/users")
        .then(function (response) {
          self.users = response.data.users;
          self.allStatuses = response.data.all_statuses;
        })
        .catch(function (err) {
          self.showError({ message: "We couldn't get users.", error: error });
        });
    },
    searchUser() {},
    open() {},
    deleteUser(id) {
      this.deleteDialog = true;
      this.deletableId = id;
      if (
        typeof localStorage.authUser != "undefined" &&
        JSON.parse(localStorage.authUser).id == id
      ) {
        this.deleteUserHeader = "Profile";
        this.deleteUserText = "your profile";
      } else {
        this.deleteUserHeader = "User";
        this.deleteUserText = "this user";
      }
    },
    confirmDeleteUser() {
      let self = this;
      axios
        .delete("/web/v1/users/" + this.deletableId)
        .then(function (response) {
          self.showSuccess(
            self.deleteUserHeader + " has been deleted successfully."
          );
          if (
            typeof localStorage.authUser != "undefined" &&
            JSON.parse(localStorage.authUser).id == self.deletableId
          ) {
            localStorage.removeItem("authUser");
            localStorage.removeItem("authUserRoles");
            localStorage.removeItem("authUserPermissions");
            router.push({ name: "auth-signin" });
          } else {
            setTimeout(() => self.initialize(), 700);
          }
          self.deletableId = null;
        })
        .catch(function (err) {
          self.showError({
            message: "We couldn't delete the user.",
            error: err,
          });
          self.deletableId = null;
        });
      this.deleteDialog = false;
    },
    cancelDeleteUser() {
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
