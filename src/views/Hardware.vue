<template>
  <div class="hardware">
    <b-container fluid>
      <b-row class="mt-4">
        <b-col lg="12" class="text-center">
          <h3>
            Hardware List
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
                @click="defaultHardwareState"
                class="float-left ml-2 dropdown-btn"
                variant="secondary"
              >Default</b-button>
              <b-button
                size="sm"
                @click="saveHardwareState"
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
            <i class="fas fa-hdd"></i>&nbsp;&nbsp;New Hardware
          </button>
        </b-col>
      </b-row>
      <b-alert variant="danger" :show="errorMsg" dismissible fade>{{ errorMessage }}</b-alert>
      <b-alert variant="success" :show="successMsg" dismissible fade>{{ successMessage }}</b-alert>
      <b-row class="mb-4">
        <b-col lg="12">
          <b-table
            ref="hardwareTable"
            :sticky-header="stickyHeader"
            responsive
            hover
            :items="items"
            :fields="visibleFields"
            class="text-center"
            thead-class="hidden_outline"
            :filter="filter"
            :filterIncludedFields="filterOn"
            :sort-compare="mySortCompare"
            table-variant="tbody-tr-class"
            @row-selected="onRowSelected"
            selectable
            :select-mode="selectMode"
            head-variant="light"
          >
            <template v-slot:cell(hardware_installed)="data">
              <div v-html="data.value" class="text-left"></div>
            </template>
            <template v-slot:cell(extra_info)="data">
              <div v-html="data.value" class="text-left"></div>
            </template>
            <template v-slot:cell(comments)="data">
              <div v-html="data.value" class="text-left"></div>
            </template>
            <template v-slot:cell(edit)="data">
              <span
                v-html="data.value"
                v-b-modal.editModal
                @click="showEditModal != showEditModal;selectHardware(data.item)"
              ></span>
            </template>
            <template v-slot:cell(delete)="data">
              <span
                v-html="data.value"
                v-b-modal.deleteModal
                @click="showDeleteModal != showDeleteModal;selectHardware(data.item)"
              ></span>
            </template>
          </b-table>
        </b-col>
      </b-row>
      <!-- Add Hardware Modal -->
      <b-row>
        <b-modal id="addModal" centered v-model="showAddModal" title="Add New Hardware" hide-footer>
          <b-form @submit.prevent="addHardware">
            <b-form-group
              label="Development name"
              label-for="development-name-input"
              invalid-feedback="Development name is required"
            >
              <b-form-input
                id="development-name-input"
                v-model="newHardware.development_name"
                required
              ></b-form-input>
            </b-form-group>
            <b-form-group
              label="Client"
              label-for="client-input"
              invalid-feedback="Client is required"
            >
              <b-form-select
                id="client-input"
                v-model="newHardware.client"
                :options="clientOptions"
                required
              ></b-form-select>
            </b-form-group>
            <b-form-group label="Hardware installed" label-for="hardware-input">
              <b-form-textarea
                id="hardware-input"
                v-model="newHardware.hardware_installed"
                placeholder="Hardware Info..."
                rows="3"
              ></b-form-textarea>
            </b-form-group>

            <b-form-group
              label="Development address"
              label-for="development-address-input"
              invalid-feedback="Development address is required"
            >
              <b-form-input
                id="development-address-input"
                v-model="newHardware.development_address"
                required
              ></b-form-input>
            </b-form-group>

            <b-form-group label="Contact" label-for="contact-input">
              <b-form-input placeholder="Name..." v-model="name" class="mb-2"></b-form-input>
              <b-row>
                <b-col lg="6">
                  <b-form-input placeholder="Phone..." v-model="phone" class="phone-input"></b-form-input>
                </b-col>
                <b-col lg="6">
                  <b-form-input placeholder="Email..." v-model="email"></b-form-input>
                </b-col>
              </b-row>
              <b-form-input v-bind:value="newHardware.contact = contactDetails" hidden></b-form-input>
            </b-form-group>
            <b-form-group
              label="Date installed"
              label-for="date-installed-input"
              invalid-feedback="Installation date is required"
            >
              <b-form-datepicker
                id="date-installed-input"
                v-model="newHardware.date_installed"
                :min="min"
                :max="max"
                locale="en-GB"
                :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
              ></b-form-datepicker>
            </b-form-group>
            <b-form-group label="Extra Info" label-for="extra-info-input">
              <b-form-textarea
                id="extra-info-input"
                v-model="newHardware.extra_info"
                placeholder="Extra Info..."
                rows="3"
              ></b-form-textarea>
            </b-form-group>
            <b-form-group label="Comments" label-for="comments-input">
              <b-form-textarea
                id="comments-input"
                v-model="newHardware.comments"
                placeholder="Write a comment..."
                rows="3"
              ></b-form-textarea>
            </b-form-group>
            <b-button class="btn-info btn-block btn-lg" type="submit">Add Hardware</b-button>
          </b-form>
        </b-modal>
      </b-row>
      <!-- Edit Hardware Modal -->
      <b-row>
        <b-modal id="editModal" centered v-model="showEditModal" hide-footer>
          <template v-slot:modal-title>
            Edit Hardware
            <span
              class="subtitle"
              v-if="currentHardware.last_change"
            >Last change by {{ currentHardware.last_change }}</span>
            <span class="time">{{ currentHardware.time_stamp }}</span>
          </template>
          <b-form @submit.prevent="updateHardware">
            <b-form-group
              label="Development name"
              label-for="development-name-input"
              invalid-feedback="Development name is required"
            >
              <b-form-input
                id="development-name-input"
                v-model="currentHardware.development_name"
                required
              ></b-form-input>
            </b-form-group>
            <b-form-group
              label="Client"
              label-for="client-input"
              invalid-feedback="Client is required"
            >
              <b-form-select
                id="client-input"
                v-model="currentHardware.client"
                :options="clientOptions"
                required
              ></b-form-select>
            </b-form-group>

            <b-form-group label="Hardware installed" label-for="hardware-input">
              <b-form-textarea
                id="hardware-input"
                v-model="currentHardware.hardware_installed"
                placeholder="Hardware Info..."
                rows="3"
              ></b-form-textarea>
            </b-form-group>

            <b-form-group
              label="Development address"
              label-for="development-address-input"
              invalid-feedback="Development address is required"
            >
              <b-form-input
                id="development-address-input"
                v-model="currentHardware.development_address"
                required
              ></b-form-input>
            </b-form-group>
            <b-form-group label="Contact" label-for="contact-input">
              <b-form-input placeholder="Name..." v-model="name" class="mb-2"></b-form-input>
              <b-row>
                <b-col lg="6">
                  <b-form-input placeholder="Phone..." v-model="phone" class="phone-input"></b-form-input>
                </b-col>
                <b-col lg="6">
                  <b-form-input placeholder="Email..." v-model="email"></b-form-input>
                </b-col>
              </b-row>
              <b-form-input v-bind:value="currentHardware.contact = contactDetails" hidden></b-form-input>
            </b-form-group>
            <b-form-group
              label="Date installed"
              label-for="date-installed-input"
              invalid-feedback="Installation date is required"
            >
              <b-form-datepicker
                id="date-installed-input"
                v-model="currentHardware.date_installed"
                :min="min"
                :max="max"
                locale="en-GB"
                :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                required
              ></b-form-datepicker>
            </b-form-group>
            <b-form-group label="Extra Info" label-for="extra-info-input">
              <b-form-textarea
                id="extra-info-input"
                v-model="currentHardware.extra_info"
                placeholder="Extra Info..."
                rows="3"
              ></b-form-textarea>
            </b-form-group>
            <b-form-group label="Comments" label-for="comments-input">
              <b-form-textarea
                id="comments-input"
                v-model="currentHardware.comments"
                placeholder="Write a comment..."
                rows="3"
              ></b-form-textarea>
            </b-form-group>
            <b-button class="btn-success btn-md float-left" type="submit">Update</b-button>
            <b-button
              class="btn-danger btn-md float-right"
              type="button"
              @click="deleteHardwareFromEditModal"
            >Delete</b-button>
          </b-form>
        </b-modal>
      </b-row>
      <!-- Delete Hardware Modal -->
      <b-row>
        <b-modal
          id="deleteModal"
          centered
          v-model="showDeleteModal"
          title="Delete hardware"
          hide-footer
        >
          <h4 class="text-danger">Are you sure you want to delete hardware?</h4>
          <hr />
          <button class="btn btn-danger btn-md" @click="deleteHardware">Yes</button>
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
  name: "Hardware",
  data() {
    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const minDate = new Date(today);
    const maxDate = new Date(today);
    maxDate.setMonth(maxDate.getMonth() + 48);
    // maxDate.setDate(15);
    return {
      stickyHeader: false,
      filter: null,
      filterOn: [],
      min: minDate,
      max: maxDate,
      errorMsg: false,
      successMsg: false,
      errorMessage: "",
      successMessage: "",
      name: "",
      phone: "",
      email: "",
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
          key: "development_name",
          label: "Development name",
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
          key: "hardware_installed",
          label: "Hardware installed",
          visible: true,
          stickyColumn: true,
          sortable: true, 
          class: "column-width"
        },
        {
          key: "development_address",
          label: "Development address",
          visible: true,
          stickyColumn: true,
          sortable: true
        },
        {
          key: "contact",
          label: "Contact",
          visible: true,
          stickyColumn: true,
          sortable: true
        },
        {
          key: "date_installed",
          label: "Date installed",
          visible: true,
          stickyColumn: true,
          sortable: true
        },
        {
          key: "extra_info",
          label: "Extra info",
          visible: true,
          stickyColumn: true,
          sortable: true, 
          class: "column-width"
        },
        {
          key: "last_change",
          label: "Last change",
          visible: true,
          stickyColumn: true,
          sortable: true
        },
        {
          key: "comments",
          label: "Comments",
          visible: true,
          stickyColumn: true,
          sortable: true, 
          class: "column-width"
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
          development_name: "",
          client: null,
          hardware_installed: "",
          development_address: "",
          contact: "",
          date_installed: "",
          extra_info: "",
          last_change: "",
          comments: "",
          time_stamp: "",
          edit: "",
          delete: ""
        }
      ],
      clientOptions: [{ value: null, text: "Please select client" }],
      showAddModal: false,
      showEditModal: false,
      showDeleteModal: false,
      hardware_list: [],
      newHardware: {
        development_name: "",
        client: null,
        hardware_installed: "",
        development_address: "",
        contact: "",
        date_installed: "",
        extra_info: "",
        comments: ""
      },
      currentHardware: {}
    };
  },
  watch: {
    showEditModal: function(val) {
      if (val === false) {
        this.name = "";
        this.phone = "";
        this.email = "";
      }
    }
  },
  mounted: function() {
    this.readHardwareState();
    this.getHardwareList();
    this.getAllClientNames();
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
    },
    contactDetails() {
      return this.name + "\n" + this.phone + "\n" + this.email;
    }
  },
  methods: {
    getHardwareList() {
      this.items = [];
      axios.get(`${API_BASE}/hardware_list.php?action=read`).then(response => {
        if (response.data.error) {
          this.errorMsg = true;
          this.errorMessage = response.data.message;
        } else {
          this.hardware_list = response.data.hardware_list;
          this.hardware_list.forEach(hardware => {
            if (hardware.hardware_installed !== null) {
              hardware.hardware_installed = hardware.hardware_installed.replace(/\n/g, "<br>");
            }
            if (hardware.date_installed === "0000-00-00" || hardware.date_installed === "1000-01-01") {
              hardware.date_installed = "";
            } else {
              hardware.date_installed = this.parseISOToMyDate(
                hardware.date_installed
              );
            }
            if (hardware.extra_info !== null) {
              hardware.extra_info = hardware.extra_info.replace(/\n/g, "<br>");
            }
            if (hardware.comments !== null) {
              hardware.comments = hardware.comments.replace(/\n/g, "<br>");
            }
            this.items.push({
              id: hardware.id,
              development_name: hardware.development_name,
              client: hardware.client,
              hardware_installed: hardware.hardware_installed,
              development_address: hardware.development_address,
              contact: hardware.contact,
              date_installed: hardware.date_installed,
              extra_info: hardware.extra_info,
              last_change: hardware.last_change,
              comments: hardware.comments,
              time_stamp: hardware.time_stamp,
              edit: "<a class='text-success'><i class='fas fa-edit'></i></a>",
              delete: "<a class='text-danger'><i class='fas fa-trash'></i></a>"
            });
          });
        }
      });
    },
    addHardware() {
      this.clearMsg();
      for (var i in this.newHardware) {
        if (this.newHardware[i] != null) {
          this.newHardware[i] = this.newHardware[i].replace(/'+/g,"''");
        }
      }
      if (this.newHardware.date_installed === "") {
        this.newHardware.date_installed = "1000-01-01";
      }
      var formData = this.toFormData(this.newHardware);
      this.showAddModal = false;
      axios
        .post(`${API_BASE}/hardware_list.php?action=create`, formData)
        .then(response => {
          this.newHardware = {
            development_name: "",
            client: null,
            hardware_installed: "",
            development_address: "",
            contact: "",
            date_installed: "",
            extra_info: ""
          };
          if (response.data.error) {
            this.errorMsg = true;
            this.errorMessage = response.data.message;
          } else {
            this.successMsg = true;
            this.successMessage = response.data.message;
            this.getHardwareList();
          }
        });
      this.name = "";
      this.phone = "";
      this.email = "";
    },
    updateHardware() {
      this.clearMsg();
      for (var i in this.currentHardware) {
        if (this.currentHardware[i] != null) {
          this.currentHardware[i] = this.currentHardware[i].replace(/'+/g,"''");         
        }
      }
      if (this.currentHardware.date_installed === "NaN-NaN-NaN" || this.currentHardware.date_installed === "") {
        this.currentHardware.date_installed = "1000-01-01";
      } 
      var formData = this.toFormData(this.currentHardware);
      formData.append("last_change", this.$store.getters.accessToken.uidUsers);
      this.showEditModal = false;
      axios
        .post(`${API_BASE}/hardware_list.php?action=update`, formData)
        .then(response => {
          this.currentHardware = {};
          if (response.data.error) {
            this.errorMsg = true;
            this.errorMessage = response.data.message;
          } else {
            this.successMsg = true;
            this.successMessage = response.data.message;
            this.getHardwareList();
          }
        });
      this.name = "";
      this.phone = "";
      this.email = "";
    },
    deleteHardware() {
      this.clearMsg();
      var formData = this.toFormData(this.currentHardware);
      this.showDeleteModal = false;
      this.showEditModal = false;
      axios
        .post(`${API_BASE}/hardware_list.php?action=delete`, formData)
        .then(response => {
          this.currentHardware = {};
          if (response.data.error) {
            this.errorMsg = true;
            this.errorMessage = response.data.message;
          } else {
            this.successMsg = true;
            this.successMessage = response.data.message;
            this.getHardwareList();
          }
        });
    },
    getAllClientNames() {
      var client_list = [];
      axios.get(`${API_BASE}/client_list.php?action=read`).then(response => {
        if (response.data.error) {
          this.errorMsg = true;
          this.errorMessage = response.data.message;
        } else {
          client_list = response.data.client_list;
          client_list.forEach(client => {
            this.clientOptions.push({
              value: client.client,
              text: client.client
            });
          });
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
    selectHardware(hardware) {
      if (hardware.hardware_installed !== null) {
        hardware.hardware_installed = hardware.hardware_installed.replace(
          /(?:<br>\s*)/g,
          "\n"
        );
      }
      if (hardware.extra_info !== null) {
        hardware.extra_info = hardware.extra_info.replace(
          /(?:<br>\s*)/g,
          "\n"
        );
      }
      if (hardware.comments !== null) {
        hardware.comments  = hardware.comments.replace(
          /(?:<br>\s*)/g,
          "\n"
        );
      }    
      if (hardware.time_stamp !== null && hardware.time_stamp.indexOf("/") == -1) {
      hardware.time_stamp = hardware.time_stamp.split("-")[1] + "/" + hardware.time_stamp.slice(8, 10) + "/" + hardware.time_stamp.split("-")[0] + " " + hardware.time_stamp.slice(11);
      }    
      if (hardware.time_stamp === "0000-00-00 00:00:00") {
        hardware.time_stamp = null;
      }      
      this.currentHardware = {
        id: hardware.id,
        development_name: hardware.development_name,
        client: hardware.client,
        hardware_installed: hardware.hardware_installed,
        development_address: hardware.development_address,
        contact: hardware.contact,
        date_installed: this.parseLocaleDateToISO(hardware.date_installed),
        extra_info: hardware.extra_info,
        last_change: hardware.last_change,
        comments: hardware.comments,
        time_stamp: this.parseISOToMyTime(hardware.time_stamp)
      };
      var str = this.currentHardware.contact;
      this.name = str.split("\n")[0];
      this.phone = str.split("\n")[1];
      this.email = str.split("\n")[2];
    },
    clearMsg() {
      this.errorMsg = false;
      this.successMsg = false;
      this.errorMessage = "";
      this.successMessage = "";
    },
    parseISOToLocaleDate(date) {
      var dateParts = date.split("-");
      var dt = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);
      return dt.toLocaleDateString("en-US", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric"
      });
    },
    parseLocaleDateToISO(date) {
      var dt = new Date(this.parseDate(date));
      return dt.getFullYear() + "-" + (dt.getMonth() + 1) + "-" + dt.getDate();
    },
    parseISOToMyDate(date) {
      var dt = new Date(date);
      return dt.getDate() + "/" + (dt.getMonth() + 1) + "/" + dt.getFullYear();
    },
    parseISOToMyTime(date) {
      if (date !== null) {
        var dt = new Date(date);

        var day = dt.getDate();
        var month = dt.getMonth() + 1;
        var year = dt.getFullYear();
        var hour = dt.getHours();
        var minute = dt.getMinutes();

        if (month.toString().length < 2) month = "0" + month;
        if (hour.toString().length < 2) hour = "0" + hour;
        if (minute.toString().length < 2) minute = "0" + minute;
        return day + "/" + month + "/" + year + " " + hour + ":" + minute;
      } else {
        return "";
      }
    },
    parseDate(date) {
      var newDate = date
        .split("/")
        .reverse()
        .join("-");
      return newDate;
    },
    mySortCompare(a, b, key) {
      var dateA, dateB;
      if (key === "date_installed") {
        if (a[key] === "") {
          dateA = new Date(this.parseISOToLocaleDate("0000-00-00"));
        } else {
          dateA = new Date(this.parseDate(a[key]));
        }
        if (b[key] === "") {
          dateB = new Date(this.parseISOToLocaleDate("0000-00-00"));
        } else {
          dateB = new Date(this.parseDate(b[key]));
        }
        return dateA - dateB;
      } else {
        return false;
      }
    },
    saveHardwareState() {
      var id = this.$store.getters.accessToken.id;
      var tableState = {
        id: this.fields[0].visible,
        development_name: this.fields[1].visible,
        client: this.fields[2].visible,
        hardware_installed: this.fields[3].visible,
        development_address: this.fields[4].visible,
        contact: this.fields[5].visible,
        date_installed: this.fields[6].visible,
        extra_info: this.fields[7].visible,
        last_change: this.fields[8].visible,
        comments: this.fields[9].visible,
        edit: this.fields[10].visible,
        delete: this.fields[11].visible
      };
      var formData = new FormData();
      formData.append("id", id);
      formData.append("table_state", JSON.stringify(tableState));
      axios
        .post(`${API_BASE}/hardware_state.php?action=update`, formData)
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
    readHardwareState() {
      var id = this.$store.getters.accessToken.id;
      axios
        .get(`${API_BASE}/hardware_state.php?action=read`, {
          params: {
            id: id
          }
        })
        .then(response => {
          if (response.data.error) {
            this.errorMsg = true;
            this.errorMessage = response.data.message;
          } else {
            if (response.data.hardware_state) {
              var tableState = JSON.parse(response.data.hardware_state);
              this.fields[0].visible = tableState.id;
              this.fields[1].visible = tableState.development_name;
              this.fields[2].visible = tableState.client;
              this.fields[3].visible = tableState.hardware_installed;
              this.fields[4].visible = tableState.development_address;
              this.fields[5].visible = tableState.contact;
              this.fields[6].visible = tableState.date_installed;
              this.fields[7].visible = tableState.extra_info;
              this.fields[8].visible = tableState.last_change;
              this.fields[9].visible = tableState.comments;
              this.fields[10].visible = tableState.edit;
              this.fields[11].visible = tableState.delete;
            } else {
              this.defaultHardwareState();
            }
          }
        });
    },
    defaultHardwareState() {
      this.fields[11] = {
          key: "delete",
          label: "Delete",
          visible: true,
          stickyColumn: true,
          sortable: false
      };
      this.fields[0].visible = false;
      this.fields[5].visible = false;
      this.fields[6].visible = false;
      this.fields[8].visible = false;
      this.fields[9].visible = false;
      this.fields[11].visible = false;
    },
    onRowSelected(items) {
      if (items.length) {
        this.showEditModal = true;
        this.selectHardware(items[0]);
        this.$refs.hardwareTable.clearSelected();
      }
    },
    deleteHardwareFromEditModal() {
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
.icon-hover:hover {
  cursor: pointer;
}
.blue-text {
  color: #007bff;
}
.tv-size {
  margin-bottom: 10px;
}
.tablet-size {
  margin-bottom: 10px;
}

@media screen and (max-width: 823px) {
  .tv-size {
    margin: 10px 0 0 0 !important;
    max-width: 50% !important;
    float: left !important;
  }
  .tv-quantity {
    margin: 10px 0 0 0 !important;
    max-width: 50% !important;
    float: right !important;
  }
  .tablet-size {
    margin: 10px 0 0 0 !important;
    max-width: 50% !important;
    float: left !important;
  }
  .tablet-quantity {
    margin: 10px 0 0 0 !important;
    max-width: 50% !important;
    float: right !important;
  }
  .computer-quantity {
    margin-top: 10px !important;
    max-width: 50% !important;
    float: right !important;
  }
}

@media screen and (max-width: 414px) {
  .phone-input {
    margin-bottom: 8px;
  }
}

@media screen and (max-width: 280px) {
  .tv-size {
    margin: 10px 0 0 0 !important;
    max-width: 45% !important;
    float: left !important;
  }
  .tv-quantity {
    margin: 10px 0 0 0 !important;
    max-width: 55% !important;
    float: right !important;
  }
  .tablet-size {
    margin: 10px 0 0 0 !important;
    max-width: 45% !important;
    float: left !important;
  }
  .tablet-quantity {
    margin: 10px 0 0 0 !important;
    max-width: 55% !important;
    float: right !important;
  }
  .computer-quantity {
    margin-top: 10px !important;
    max-width: 55% !important;
    float: right !important;
  }
}
</style>

