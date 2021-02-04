<template>
  <div class="projects">
    <b-container fluid>
      <b-row class="mt-4">
        <b-col lg="12" class="text-center">
          <h3>
            Client List
            <b-dropdown
              id="dropdown-1"
              class="ml-1"
              variant="info"
              text="Show/Hide columns"
              ref="dropdown"
            >
              <b-checkbox
                :disabled="visibleFields.length == 1 && field.visible"
                v-for="field in fields"
                :key="field.key"
                v-model="field.visible"
              >
                <b-dropdown-text>{{ field.label }}</b-dropdown-text>
              </b-checkbox>
              <b-dropdown-divider></b-dropdown-divider>
              <b-button
                size="sm"
                @click="defaultClientsState"
                class="float-left ml-2 dropdown-btn"
                variant="secondary"
              >Default</b-button>
              <b-button
                size="sm"
                @click="saveClientsState"
                class="float-right mr-2 dropdown-btn"
                variant="success"
              >Save</b-button>
            </b-dropdown>
          </h3>
        </b-col>
      </b-row>
      <b-row class="mt-4">
        <b-col sm="6" lg="3" class="search">
          <b-form-group>
            <b-input-group>
              <b-form-input
                v-model="filter"
                type="search"
                id="filterInput"
                placeholder="Search..."
                class="shadow-none"
              ></b-form-input>
              <b-input-group-append>
                <b-button :disabled="!filter" @click="filter = ''">Clear</b-button>
              </b-input-group-append>
            </b-input-group>
          </b-form-group>
        </b-col>
      </b-row>
      <b-row>
        <b-col xs="6">
          <div class="mt-3">
            <b-form-checkbox v-model="stickyHeader" inline>Sticky header</b-form-checkbox>
          </div>
        </b-col>
        <b-col xs="6">
          <button
            class="btn btn-info float-right shadow-none mb-2"
            v-b-modal.addModal
            @click="showAddModal = !showAddModal"
            v-if="adminIsAuthenticated"
          >
            <i class="fas fa-user-tie"></i>&nbsp;&nbsp;New Client
          </button>
        </b-col>
      </b-row>
      <b-alert variant="danger" :show="errorMsg" dismissible fade>{{ errorMessage }}</b-alert>
      <b-alert variant="success" :show="successMsg" dismissible fade>{{ successMessage }}</b-alert>
      <b-row class="mb-4">
        <b-col lg="12">
          <b-table
            ref="clientsTable"
            :sticky-header="stickyHeader"
            responsive
            hover
            :items="items"
            :fields="visibleFields"
            class="text-center"
            thead-class="hidden_outline"
            :filter="filter"
            :filterIncludedFields="filterOn"
            table-variant="tbody-tr-class"
            @row-selected="onRowSelected"
            selectable
            :select-mode="selectMode"
            head-variant="light"
          >
            <template v-slot:cell(edit)="data">
              <span
                v-html="data.value"
                v-b-modal.editModal
                @click="showEditModal != showEditModal;selectClient(data.item)"
              ></span>
            </template>
            <template v-slot:cell(delete)="data">
              <span
                v-html="data.value"
                v-b-modal.deleteModal
                @click="showDeleteModal != showDeleteModal;selectClient(data.item)"
              ></span>
            </template>
          </b-table>
        </b-col>
      </b-row>
      <!-- Add Client Modal -->
      <b-row>
        <b-modal id="addModal" centered v-model="showAddModal" title="Add New Client" hide-footer>
          <b-form @submit.prevent="addClient">
            <b-form-group
              label="Client"
              label-for="client-input"
              invalid-feedback="Client is required"
            >
              <b-form-input id="client-input" v-model="newClient.client" required></b-form-input>
            </b-form-group>
            <b-form-group
              label="Contact Details"
              label-for="contact-details-input"
              invalid-feedback="Contact Details is required"
            >
              <b-form-input id="contact-details-input" v-model="newClient.contact_details" required></b-form-input>
            </b-form-group>
            <b-form-group
              label="Invoice Contact"
              label-for="invoice-contact-input"
              invalid-feedback="Invoice Contact is required"
            >
              <b-form-input id="invoice-contact-input" v-model="newClient.invoice_contact" required></b-form-input>
            </b-form-group>
            <b-form-group
              label="Address"
              label-for="address-input"
              invalid-feedback="Address is required"
            >
              <b-form-input id="address-input" v-model="newClient.address" required></b-form-input>
            </b-form-group>
            <b-button class="btn-info btn-block btn-lg" type="submit">Add Client</b-button>
          </b-form>
        </b-modal>
      </b-row>
      <!-- Edit Client Modal -->
      <b-row>
        <b-modal id="editModal" centered v-model="showEditModal" title="Edit Client" hide-footer>
          <b-form @submit.prevent="updateClient">
            <b-form-group
              label="Client"
              label-for="client-input"
              invalid-feedback="Client is required"
            >
              <b-form-input id="client-input" v-model="currentClient.client" required></b-form-input>
            </b-form-group>
            <b-form-group
              label="Contact Details"
              label-for="contact-details-input"
              invalid-feedback="Contact Details is required"
            >
              <b-form-input
                id="contact-details-input"
                v-model="currentClient.contact_details"
                required
              ></b-form-input>
            </b-form-group>
            <b-form-group
              label="Invoice Contact"
              label-for="invoice-contact-input"
              invalid-feedback="Invoice Contact is required"
            >
              <b-form-input
                id="invoice-contact-input"
                v-model="currentClient.invoice_contact"
                required
              ></b-form-input>
            </b-form-group>
            <b-form-group
              label="Address"
              label-for="address-input"
              invalid-feedback="Address is required"
            >
              <b-form-input id="address-input" v-model="currentClient.address" required></b-form-input>
            </b-form-group>
            <b-button class="btn-success btn-md float-left" type="submit">Update</b-button>
            <b-button
              class="btn-danger btn-md float-right"
              type="button"
              @click="deleteProjectFromEditModal"
            >Delete</b-button>
          </b-form>
        </b-modal>
      </b-row>
      <!-- Delete Client Modal -->
      <b-row>
        <b-modal
          id="deleteModal"
          centered
          v-model="showDeleteModal"
          title="Delete client"
          hide-footer
        >
          <h4 class="text-danger">Are you sure you want to delete client?</h4>
          <hr />
          <button class="btn btn-danger btn-md" @click="deleteClient">Yes</button>
          &nbsp;&nbsp;&nbsp;&nbsp;
          <button
            class="btn btn-secondary btn-md"
            @click="showDeleteModal=false"
          >No</button>
        </b-modal>
      </b-row>
    </b-container>
  </div>
</template>

<script>
import API_BASE from "../api_base";
export default {
  name: "Clients",
  data() {
    return {
      stickyHeader: false,
      filter: null,
      filterOn: [],
      errorMsg: false,
      successMsg: false,
      errorMessage: "",
      successMessage: "",
      modes: ["multi", "single", "range"],
      selectMode: "single",
      fields: [
        {
          key: "id",
          label: "Id",
          visible: true,
          stickyColumn: true,
          sortable: true
        },
        {
          key: "client",
          label: "Client",
          visible: true,
          stickyColumn: true,
          sortable: true
        },
        {
          key: "contact_details",
          label: "Contact details",
          visible: true,
          stickyColumn: true,
          sortable: true
        },
        {
          key: "invoice_contact",
          label: "Invoice contact",
          visible: true,
          stickyColumn: true,
          sortable: true
        },
        {
          key: "address",
          label: "Address",
          visible: true,
          stickyColumn: true,
          sortable: true
        },
        {
          key: "edit",
          label: "Edit",
          visible: true,
          stickyColumn: true,
          sortable: false
        },
        {
          key: "delete",
          label: "Delete",
          visible: true,
          stickyColumn: true,
          sortable: false
        }
      ],
      items: [
        {
          id: "",
          client: "",
          contact_details: "",
          invoice_contact: "",
          address: "",
          edit: "",
          delete: ""
        }
      ],
      showAddModal: false,
      showEditModal: false,
      showDeleteModal: false,
      client_list: [],
      newClient: {
        client: "",
        contact_details: "",
        invoice_contact: "",
        address: ""
      },
      currentClient: {}
    };
  },
  mounted: function() {
    this.readClientsState();
    this.getAllClients();
    if (!this.adminIsAuthenticated) {
      this.fields.pop();
    }
  },
  computed: {
    adminIsAuthenticated() {
      return (
        this.$store.getters.accessToken !== null &&
        this.$store.getters.accessToken !== undefined &&
        this.$store.getters.accessToken.role === "Admin"
      );
    },
    visibleFields() {
      return this.fields.filter(field => field.visible);
    }
  },
  methods: {
    getAllClients() {
      this.items = [];
      axios.get(`${API_BASE}/client_list.php?action=read`).then(response => {
        if (response.data.error) {
          this.errorMsg = true;
          this.errorMessage = response.data.message;
        } else {
          this.client_list = response.data.client_list;
          this.client_list.forEach(client => {
            this.items.push({
              id: client.id,
              client: client.client,
              contact_details: client.contact_details,
              invoice_contact: client.invoice_contact,
              address: client.address,
              edit: "<a class='text-success'><i class='fas fa-edit'></i></a>",
              delete: "<a class='text-danger'><i class='fas fa-trash'></i></a>"
            });
          });
        }
      });
    },
    addClient() {
      this.clearMsg();
      var formData = this.toFormData(this.newClient);
      this.showAddModal = false;
      axios
        .post(`${API_BASE}/client_list.php?action=create`, formData)
        .then(response => {
          this.newClient = {
            client: "",
            contact_details: "",
            invoice_contact: "",
            address: ""
          };
          if (response.data.error) {
            this.errorMsg = true;
            this.errorMessage = response.data.message;
          } else {
            this.successMsg = true;
            this.successMessage = response.data.message;
            this.getAllClients();
          }
        });
    },
    updateClient() {
      this.clearMsg();
      var formData = this.toFormData(this.currentClient);
      this.showEditModal = false;
      axios
        .post(`${API_BASE}/client_list.php?action=update`, formData)
        .then(response => {
          this.currentClient = {};
          if (response.data.error) {
            this.errorMsg = true;
            this.errorMessage = response.data.message;
          } else {
            this.successMsg = true;
            this.successMessage = response.data.message;
            this.getAllClients();
          }
        });
    },
    deleteClient() {
      this.clearMsg();
      var formData = this.toFormData(this.currentClient);
      this.showDeleteModal = false;
      this.showEditModal = false;
      axios
        .post(`${API_BASE}/client_list.php?action=delete`, formData)
        .then(response => {
          this.currentClient = {};
          if (response.data.error) {
            this.errorMsg = true;
            this.errorMessage = response.data.message;
          } else {
            this.successMsg = true;
            this.successMessage = response.data.message;
            this.getAllClients();
          }
        });
    },
    toFormData(obj) {
      var formData = new FormData();
      for (var i in obj) {
        formData.append(i, obj[i]);
      }
      return formData;
    },
    selectClient(client) {
      this.currentClient = client;
    },
    clearMsg() {
      this.errorMsg = false;
      this.successMsg = false;
      this.errorMessage = "";
      this.successMessage = "";
    },
    saveClientsState() {
      var id = this.$store.getters.accessToken.id;
      var tableState = {
        id: this.fields[0].visible,
        client: this.fields[1].visible,
        contact_details: this.fields[2].visible,
        invoice_contact: this.fields[3].visible,
        address: this.fields[4].visible,
        edit: this.fields[5].visible,
        delete: this.fields[6].visible
      };
      var formData = new FormData();
      formData.append("id", id);
      formData.append("table_state", JSON.stringify(tableState));
      axios
        .post(`${API_BASE}/clients_state.php?action=update`, formData)
        .then(response => {
          if (response.data.error) {
            this.errorMsg = true;
            this.errorMessage = response.data.message;
          } else {
            this.successMsg = true;
            this.successMessage = response.data.message;
          }
        });

      this.$refs.dropdown.hide(true);
    },
    readClientsState() {
      var id = this.$store.getters.accessToken.id;
      axios
        .get(`${API_BASE}/clients_state.php?action=read`, {
          params: {
            id: id
          }
        })
        .then(response => {
          if (response.data.error) {
            this.errorMsg = true;
            this.errorMessage = response.data.message;
          } else {
            if (response.data.clients_state) {
              var tableState = JSON.parse(response.data.clients_state);
              this.fields[0].visible = tableState.id;
              this.fields[1].visible = tableState.client;
              this.fields[2].visible = tableState.contact_details;
              this.fields[3].visible = tableState.invoice_contact;
              this.fields[4].visible = tableState.address;
              this.fields[5].visible = tableState.edit;
              this.fields[6].visible = tableState.delete;
            } else {
              this.defaultClientsState();
            }
          }
        });
    },
    defaultClientsState() {
       this.fields[6] = {
          key: "delete",
          label: "Delete",
          visible: true,
          stickyColumn: true,
          sortable: false
      };
      this.fields[0].visible = false;
      this.fields[6].visible = false;
    },
    onRowSelected(items) {
      if (items.length) {
        this.showEditModal = true;
        this.selectClient(items[0]);
        this.$refs.clientsTable.clearSelected();
      }
    },
    deleteProjectFromEditModal() {
      this.showDeleteModal = true;
    }
  }
};
</script>

<style scoped>
span {
  outline: none;
  border: none;
}
</style>

