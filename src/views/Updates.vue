<template>
  <div class="updates">
    <div class="colours-legend">
      <div class="square red"></div>Urgent
      <br />
      <div class="square orange"></div>ASAP
      <br />
      <div class="square blue"></div>Non-critical
      <br />
      <div class="square grey"></div>Completed
      <span style="font-size:12px">(status)</span>
    </div>
    <b-container fluid>
      <b-row class="mt-4">
        <b-col lg="12" class="text-center">
          <h3>
            Updates Hub
            <b-button-group>
              <b-dropdown
                id="dropdown-1"
                class="ml-1"
                variant="primary"
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
                  @click="defaultUpdatesState"
                  class="float-left ml-2 dropdown-btn"
                  variant="secondary"
                >Default</b-button>
                <b-button
                  size="sm"
                  @click="saveUpdatesState"
                  class="float-right mr-2 dropdown-btn"
                  variant="success"
                >Save</b-button>
              </b-dropdown>
            </b-button-group>
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
          <div class="mb-2">
            <b-form-checkbox v-model="stickyHeader" inline>Sticky header</b-form-checkbox>
          </div>
          <div class="float-left mb-2">
            <b-form-checkbox
              v-model="uncompleted"
              name="check-button"
              switch
              v-if="uncompleted"
            >Uncompleted</b-form-checkbox>
            <b-form-checkbox v-model="uncompleted" name="check-button" switch v-else>All</b-form-checkbox>
          </div>
        </b-col>
        <b-col xs="6">
          <button
            class="btn btn-info float-right shadow-none mt-3"
            v-b-modal.addModal
            @click="showAddModal = !showAddModal"
            v-if="adminIsAuthenticated"
          >
            <i class="fas fa-layer-group"></i>&nbsp;&nbsp;New Update
          </button>
        </b-col>
      </b-row>
      <b-alert variant="danger" :show="errorMsg" dismissible fade>{{ errorMessage }}</b-alert>
      <b-alert variant="success" :show="successMsg" dismissible fade>{{ successMessage }}</b-alert>
      <b-row class="mb-4">
        <b-col lg="12">
          <b-table
            ref="updatesTable"
            :sticky-header="stickyHeader"
            responsive
            hover
            :items="uncompletedUpdates"
            :fields="visibleFields"
            class="text-center"
            thead-class="hidden_outline"
            :filter="filter"
            :filterIncludedFields="filterOn"
            :sort-compare="mySortCompare"
            :tbody-tr-class="rowClass"
            table-variant="tbody-tr-class"
            @row-selected="onRowSelected"
            selectable
            :select-mode="selectMode"
            head-variant="light"
          >
            <template v-slot:cell(description)="data">
              <div v-html="data.value" class="text-left"></div>
            </template>
            <template v-slot:cell(comments)="data">
              <div v-html="data.value" class="text-left"></div>
            </template>
            <template v-slot:cell(solutions)="data">
              <div v-html="data.value" class="text-left"></div>
            </template>
            <template v-slot:cell(edit)="data">
              <span
                v-html="data.value"
                v-b-modal.editModal
                @click="showEditModal != showEditModal;selectUpdate(data.item)"
              ></span>
            </template>
            <template v-slot:cell(delete)="data">
              <span
                v-html="data.value"
                v-b-modal.deleteModal
                @click="showDeleteModal != showDeleteModal;selectUpdate(data.item)"
              ></span>
            </template>
          </b-table>
        </b-col>
      </b-row>
      <!-- Add Update Modal -->
      <b-row>
        <b-modal id="addModal" centered v-model="showAddModal" title="Add New Update" hide-footer>
          <b-form @submit.prevent="addUpdate">
            <b-form-group
              label="Development"
              label-for="development-input"
              invalid-feedback="Development is required"
            >
              <b-form-input id="development-input" v-model="newUpdate.development" required></b-form-input>
            </b-form-group>
            <b-form-group
              label="Description"
              label-for="description-input"
              invalid-feedback="Description is required"
            >
              <b-form-textarea
                id="description-input"
                v-model="newUpdate.description"
                placeholder="Write a description..."
                rows="3"
                required
              ></b-form-textarea>
            </b-form-group>
            <b-form-group label="Date entered" label-for="date-entered-input">
              <b-form-datepicker
                id="date-entered-input"
                v-model="newUpdate.date_entered"
                :min="min"
                :max="max"
                locale="en-GB"
                :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                disabled
              ></b-form-datepicker>
            </b-form-group>
            <b-form-group
              label="Level of urgency"
              label-for="level-input"
              invalid-feedback="Level of urgency is required"
            >
              <b-form-select
                id="level-input"
                v-model="newUpdate.level_of_urgency"
                :options="levelOptions"
              ></b-form-select>
            </b-form-group>
            <b-form-group label="Estimated completion" label-for="estimated-completion-input">
              <b-form-datepicker
                id="estimated-completion-input"
                v-model="newUpdate.estimated_completion"
                :min="min"
                :max="max"
                locale="en-GB"
                :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
              ></b-form-datepicker>
            </b-form-group>
            <b-form-group
              label="Status"
              label-for="status-input"
            >
              <b-form-select
                id="status-input"
                v-model="newUpdate.status"
                :options="statusOptions"
              ></b-form-select>
            </b-form-group>
            <b-form-group label="Comments" label-for="comments-input">
              <b-form-textarea
                id="comments-input"
                v-model="newUpdate.comments"
                placeholder="Write a comment..."
                rows="3"
              ></b-form-textarea>
            </b-form-group>
            <b-form-group label="Solutions" label-for="solutions-input">
              <b-form-textarea
                id="solutions-input"
                v-model="newUpdate.solutions"
                placeholder="Write a solution..."
                rows="3"
              ></b-form-textarea>
            </b-form-group>
            <b-button class="btn-info btn-block btn-lg" type="submit">Add Update</b-button>
          </b-form>
        </b-modal>
      </b-row>
      <!-- Edit Update Modal -->
      <b-row>
        <b-modal id="editModal" centered v-model="showEditModal" hide-footer>
          <template v-slot:modal-title>
            Edit Update
            <span
              class="subtitle"
              v-if="currentUpdate.last_change"
            >Last change by {{ currentUpdate.last_change }}</span>
            <span class="time">{{ currentUpdate.time_stamp }}</span>
          </template>
          <b-form @submit.prevent="updateUpdate">
            <b-form-group
              label="Development"
              label-for="development-input"
              invalid-feedback="Development is required"
            >
              <b-form-input id="development-input" v-model="currentUpdate.development" required></b-form-input>
            </b-form-group>
            <b-form-group
              label="Description"
              label-for="description-input"
              invalid-feedback="Description is required"
            >
              <b-form-textarea
                id="description-input"
                v-model="currentUpdate.description"
                placeholder="Write a description..."
                rows="3"
                required
              ></b-form-textarea>
            </b-form-group>
            <b-form-group label="Date entered" label-for="date-entered-input">
              <b-form-datepicker
                id="date-entered-input"
                v-model="currentUpdate.date_entered"
                :min="min"
                :max="max"
                locale="en-GB"
                :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
                disabled
              ></b-form-datepicker>
            </b-form-group>
            <b-form-group
              label="Level of urgency"
              label-for="level-input"
              invalid-feedback="Level of urgency is required"
            >
              <b-form-select
                id="level-input"
                v-model="currentUpdate.level_of_urgency"
                :options="levelOptions"
              ></b-form-select>
            </b-form-group>
            <b-form-group label="Estimated completion" label-for="estimated-completion-input">
              <b-form-datepicker
                id="estimated-completion-input"
                v-model="currentUpdate.estimated_completion"
                :min="min"
                :max="max"
                locale="en-GB"
                :date-format-options="{ year: 'numeric', month: 'numeric', day: 'numeric' }"
              ></b-form-datepicker>
            </b-form-group>
            <b-form-group
              label="Status"
              label-for="status-input"
            >
              <b-form-select
                id="status-input"
                v-model="currentUpdate.status"
                :options="statusOptions"
              ></b-form-select>
            </b-form-group>
            <b-form-group label="Comments" label-for="comments-input">
              <b-form-textarea
                id="comments-input"
                v-model="currentUpdate.comments"
                placeholder="Write a comment..."
                rows="3"
              ></b-form-textarea>
            </b-form-group>
            <b-form-group label="Solutions" label-for="solutions-input">
              <b-form-textarea
                id="solutions-input"
                v-model="currentUpdate.solutions"
                placeholder="Write a solution..."
                rows="3"
              ></b-form-textarea>
            </b-form-group>
            <b-button class="btn-success btn-md float-left" type="submit">Update</b-button>
            <b-button
              class="btn-danger btn-md float-right"
              type="button"
              @click="deleteUpdateFromEditModal"
            >Delete</b-button>
          </b-form>
        </b-modal>
      </b-row>
      <!-- Delete Update Modal -->
      <b-row>
        <b-modal
          id="deleteModal"
          centered
          v-model="showDeleteModal"
          title="Delete update"
          hide-footer
        >
          <h4 class="text-danger">Are you sure you want to delete update?</h4>
          <hr />
          <button class="btn btn-danger btn-md" @click="deleteUpdate">Yes</button>
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
      uncompleted: true,
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
          key: "development",
          label: "Development",
          visible: true,
          stickyColumn: true,
          sortable: true
        },
        {
          key: "description",
          label: "Description",
          visible: true,
          stickyColumn: true,
          sortable: true, 
          class: "column-width"
        },
        {
          key: "date_entered",
          label: "Date entered",
          visible: true,
          stickyColumn: true,
          sortable: true
        },
        {
          key: "level_of_urgency",
          label: "Level of urgency",
          visible: true,
          stickyColumn: true,
          sortable: true
        },
        {
          key: "estimated_completion",
          label: "Estimated completion",
          visible: true,
          stickyColumn: true,
          sortable: true
        },
        {
          key: "status",
          label: "Status",
          visible: true,
          stickyColumn: true,
          sortable: true
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
          key: "solutions",
          label: "Solutions",
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
          development: "",
          description: "",
          date_entered: "",
          level_of_urgency: null,
          estimated_completion: "",
          status: null,
          last_change: "",
          comments: "",
          time_stamp: "",
          solutions: "",
          edit: "",
          delete: ""
        }
      ],
      levelOptions: [
        { value: null, text: "Please select level of urgency" },
        { value: "Urgent", text: "Urgent" },
        { value: "ASAP", text: "ASAP" },
        { value: "Non-critical", text: "Non-critical" }
      ],
      statusOptions: [
        { value: null, text: "Please select update status" },
        { value: "Awaiting materials", text: "Awaiting materials" },
        { value: "Needs extra info", text: "Needs extra info" },
        { value: "In progress", text: "In progress" },
        { value: "Completed", text: "Completed" }
      ],
      showAddModal: false,
      showEditModal: false,
      showDeleteModal: false,
      update_list: [],
      newUpdate: {
        development: "",
        description: "",
        date_entered: new Date().toISOString().substring(0, 10),
        level_of_urgency: null,
        estimated_completion: "",
        status: null,
        comments: "",
        solutions: ""
      },
      currentUpdate: {}
    };
  },
  mounted: function() {
    this.readUpdatesState();
    this.getUpdateList();
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
    uncompletedUpdates() {
      if (this.uncompleted) {
        return this.items.filter(
          uncompleted => uncompleted.status !== "Completed"
        );
      } else {
        return this.items;
      }
    }
  },
  methods: {
    getUpdateList() {
      this.items = [];
      axios.get(`${API_BASE}/update_list.php?action=read`).then(response => {
        if (response.data.error) {
          this.errorMsg = true;
          this.errorMessage = response.data.message;
        } else {
          this.update_list = response.data.update_list;
          this.update_list.forEach(update => {
            if (update.description !== null) {
              update.description = update.description.replace(/\n/g, "<br>");
            }
            if (update.date_entered === "0000-00-00" || update.date_entered === "1000-01-01") {
              update.date_entered = "";
            } else {
              update.date_entered = this.parseISOToMyDate(update.date_entered);
            }
            if (update.level_of_urgency === "null") {
              update.level_of_urgency = "";
            }
            if (update.solutions === "null") {
              update.solutions = "";
            }
            if (update.estimated_completion === "0000-00-00" || update.estimated_completion === "1000-01-01") {
              update.estimated_completion = "";
            } else {
              update.estimated_completion = this.parseISOToMyDate(
                update.estimated_completion
              );
            }
            if (update.status === "null") {
              update.status = "";
            }
            if (update.comments !== null) {
              update.comments = update.comments.replace(/\n/g, "<br>");
            }
            if (update.solutions !== null) {
              update.solutions = update.solutions.replace(/\n/g, "<br>");
            }
            this.items.push({
              id: update.id,
              development: update.development,
              description: update.description,
              date_entered: update.date_entered,
              level_of_urgency: update.level_of_urgency,
              estimated_completion: update.estimated_completion,
              status: update.status,
              last_change: update.last_change,
              comments: update.comments,
              time_stamp: update.time_stamp,
              solutions: update.solutions,
              edit: "<a class='text-success'><i class='fas fa-edit'></i></a>",
              delete: "<a class='text-danger'><i class='fas fa-trash'></i></a>"
            });
          });
        }
      });
    },
    addUpdate() {
      this.clearMsg();
      for (var i in this.newUpdate) {
        if (this.newUpdate[i] != null) {
          this.newUpdate[i] = this.newUpdate[i].replace(/'+/g,"''");
        }
      }
      if (this.newUpdate.estimated_completion === "") {
        this.newUpdate.estimated_completion = "1000-01-01";
      }
      var formData = this.toFormData(this.newUpdate);
      this.showAddModal = false;
      axios
        .post(`${API_BASE}/update_list.php?action=create`, formData)
        .then(response => {
          this.newUpdate = {
            development: "",
            description: "", 
            date_entered: new Date().toISOString().substring(0, 10),
            level_of_urgency: null,
            estimated_completion: "",
            status: null,
            comments: "",
            solutions: ""
          };
          if (response.data.error) {
            this.errorMsg = true;
            this.errorMessage = response.data.message;
          } else {
            this.successMsg = true;
            this.successMessage = response.data.message;
            this.getUpdateList();
          }
        });
    },
    updateUpdate() {
      this.clearMsg();
      for (var i in this.currentUpdate) {
        if (this.currentUpdate[i] != null) {
          this.currentUpdate[i] = this.currentUpdate[i].replace(/'+/g,"''");         
        }
      }
      if (this.currentUpdate.estimated_completion === "NaN-NaN-NaN" || this.currentUpdate.estimated_completion === "") {
        this.currentUpdate.estimated_completion = "1000-01-01";
      } 
      var formData = this.toFormData(this.currentUpdate);
      formData.append("last_change", this.$store.getters.accessToken.uidUsers);
      this.showEditModal = false;
      axios
        .post(`${API_BASE}/update_list.php?action=update`, formData)
        .then(response => {
          this.currentUpdate = {};
          if (response.data.error) {
            this.errorMsg = true;
            this.errorMessage = response.data.message;
          } else {
            this.successMsg = true;
            this.successMessage = response.data.message;
            this.getUpdateList();
          }
        });
    },
    deleteUpdate() {
      this.clearMsg();
      var formData = this.toFormData(this.currentUpdate);
      this.showDeleteModal = false;
      this.showEditModal = false;
      axios
        .post(`${API_BASE}/update_list.php?action=delete`, formData)
        .then(response => {
          this.currentUpdate = {};
          if (response.data.error) {
            this.errorMsg = true;
            this.errorMessage = response.data.message;
          } else {
            this.successMsg = true;
            this.successMessage = response.data.message;
            this.getUpdateList();
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
    selectUpdate(update) {
      if (update.description !== null) {
        update.description = update.description.replace(/\n/g, "<br>");
      }
      if (update.level_of_urgency === "") {
        update.level_of_urgency = null;
      }
      if (update.status === "") {
        update.status = null;
      }
      if (update.comments !== null) {
        update.comments = update.comments.replace(/\n/g, "<br>");
      }
      if (update.time_stamp !== null && update.time_stamp.indexOf("/") == -1) {
        update.time_stamp = update.time_stamp.split("-")[1] + "/" + update.time_stamp.slice(8, 10) + "/" + update.time_stamp.split("-")[0] + " " + update.time_stamp.slice(11);
      }    
      if (update.time_stamp === "0000-00-00 00:00:00") {
        update.time_stamp = null;
      }     
      if (update.solutions !== null) {
        update.solutions = update.solutions.replace(/\n/g, "<br>");
      }     
      this.currentUpdate = {
        id: update.id,
        development: update.development,
        description: update.description,
        date_entered: this.parseLocaleDateToISO(update.date_entered),
        level_of_urgency: update.level_of_urgency,
        estimated_completion: this.parseLocaleDateToISO(
          update.estimated_completion
        ),
        status: update.status,
        last_change: update.last_change,
        comments: update.comments, 
        time_stamp: this.parseISOToMyTime(update.time_stamp),
        solutions: update.solutions
      };
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
      if (key === "date_entered" || key === "estimated_completion") {
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
    rowClass(item, type) {
      if (!item || type !== "row") return;
      if (item.status === "Completed") {
        return "table-dark";
      } else if (item.level_of_urgency === "Urgent") {
        return "table-danger";
      } else if (item.level_of_urgency === "ASAP") {
        return "table-border-warning";
      } else if (item.level_of_urgency === "Non-critical") {
        return "table-border-primary";
      }
    },
    saveUpdatesState() {
      var id = this.$store.getters.accessToken.id;
      var tableState = {
        id: this.fields[0].visible,
        development: this.fields[1].visible,
        description: this.fields[2].visible,
        date_entered: this.fields[3].visible,
        level_of_urgency: this.fields[4].visible,
        estimated_completion: this.fields[5].visible,
        status: this.fields[6].visible,
        last_change: this.fields[7].visible,
        comments: this.fields[8].visible,
        solutions: this.fields[9].visible,
        edit: this.fields[10].visible,
        delete: this.fields[11].visible
      };
      var formData = new FormData();
      formData.append("id", id);
      formData.append("table_state", JSON.stringify(tableState));
      axios
        .post(`${API_BASE}/updates_state.php?action=update`, formData)
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
    readUpdatesState() {
      var id = this.$store.getters.accessToken.id;
      axios
        .get(`${API_BASE}/updates_state.php?action=read`, {
          params: {
            id: id
          }
        })
        .then(response => {
          if (response.data.error) {
            this.errorMsg = true;
            this.errorMessage = response.data.message;
          } else {
            if (response.data.updates_state) {
              var tableState = JSON.parse(response.data.updates_state);
              this.fields[0].visible = tableState.id;
              this.fields[1].visible = tableState.development;
              this.fields[2].visible = tableState.description;
              this.fields[3].visible = tableState.date_entered;
              this.fields[4].visible = tableState.level_of_urgency;
              this.fields[5].visible = tableState.estimated_completion;
              this.fields[6].visible = tableState.status;
              this.fields[7].visible = tableState.last_change;
              this.fields[8].visible = tableState.comments;
              this.fields[9].visible = tableState.solutions;
              this.fields[10].visible = tableState.edit;
              this.fields[11].visible = tableState.delete;
            } else {
              this.defaultUpdatesState();
            }
          }
        });
    },
    defaultUpdatesState() {
      this.fields[11] = {
          key: "delete",
          label: "Delete",
          visible: true,
          stickyColumn: true,
          sortable: false
      };
      this.fields[0].visible = false;
      this.fields[3].visible = false;
      this.fields[5].visible = false;
      this.fields[7].visible = false;
      this.fields[10].visible = false;
      this.fields[11].visible = false;
    },
    onRowSelected(items) {
      this.showEditModal = true;
      this.selectUpdate(items[0]);
    },
    onRowSelected(items) {
      if (items.length) {
        this.showEditModal = true;
        this.selectUpdate(items[0]);
        this.$refs.updatesTable.clearSelected();
      }
    },
    deleteUpdateFromEditModal() {
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
.colours-legend {
  position: absolute;
  top: 80px;
  right: 25px;
  font-size: 12px;
}
.red {
  background-color: #f5c6cb;
}
.orange {
  background-color: #ffe8a1;
}
.blue {
  background: #b8daff;
}
.grey {
  background-color: #b9bbbe;
}
</style>

