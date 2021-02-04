<template>
  <div class="projects">
    <div class="colours-legend">
      <div class="square blue"></div>Pending approval
      <br />
      <div class="square light-blue"></div>Awaiting materials
      <br />
      <div class="square green"></div>Materials recieved*
      <br />
      <div class="square yellow"></div>Production
      <br />
      <div class="square dark-orange"></div>Update Live
      <br />
      <div class="square grey"></div>Completed
      <br />
      <div class="square red"></div>On hold
    </div>
    <b-container fluid>
      <b-row class="mt-4">
        <b-col lg="12" class="text-center">
          <h3>
            Project list
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
                @click="defaultProjectState"
                class="float-left ml-2 dropdown-btn"
                variant="secondary"
              >Default</b-button>
              <b-button
                size="sm"
                @click="saveProjectsState"
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
          <div class="mb-2">
            <b-form-checkbox v-model="stickyHeader" inline>Sticky header</b-form-checkbox>
          </div>
          <div class="mb-2">
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
            <i class="fas fa-clipboard-list"></i>&nbsp;&nbsp;New Project
          </button>
        </b-col>
      </b-row>
      <b-alert variant="danger" :show="errorMsg" dismissible fade>
        {{
        errorMessage
        }}
      </b-alert>
      <b-alert variant="success" :show="successMsg" dismissible fade>
        {{
        successMessage
        }}
      </b-alert>
      <b-row class="mb-4">
        <b-col lg="12">
          <b-table
            ref="projectsTable"
            :sticky-header="stickyHeader"
            responsive
            hover
            :items="uncompletedProjects"
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
            <template v-slot:cell(edit)="data">
              <span
                v-html="data.value"
                v-b-modal.editModal
                @click="
                  showEditModal != showEditModal;
                  selectProject(data.item);
                "
              ></span>
            </template>
            <template v-slot:cell(delete)="data">
              <span
                v-html="data.value"
                v-b-modal.deleteModal
                @click="
                  showDeleteModal != showDeleteModal;
                  selectProject(data.item);
                "
              ></span>
            </template>
          </b-table>
        </b-col>
      </b-row>
      <!-- Add Project Modal -->
      <b-row>
        <b-modal id="addModal" centered v-model="showAddModal" title="Add New Project" hide-footer>
          <b-form @submit.prevent="addProject">
            <b-form-group label="Type" label-for="type-input" invalid-feedback="Type is required">
              <b-form-select
                id="type-input"
                v-model="newProject.type"
                :options="typeOptions"
                required
              ></b-form-select>
            </b-form-group>
            <b-form-group
              label="Title"
              label-for="title-input"
              invalid-feedback="Title is required"
            >
              <b-form-input id="title-input" v-model="newProject.title" required></b-form-input>
            </b-form-group>
            <b-form-group
              label="Client"
              label-for="client-input"
              invalid-feedback="Client is required"
            >
              <b-form-select
                id="client-input"
                v-model="newProject.client"
                :options="clientOptions"
                required
              ></b-form-select>
            </b-form-group>
            <b-form-group
              label="Description"
              label-for="description-input"
              invalid-feedback="Description is required"
            >
              <b-form-textarea
                id="description-input"
                v-model="newProject.description"
                placeholder="Write a description..."
                rows="3"
                required
              ></b-form-textarea>
            </b-form-group>
            <b-form-group label="Start date" label-for="start-date-input">
              <b-form-datepicker
                id="start-date-input"
                v-model="newProject.start_date"
                :min="min"
                :max="max"
                locale="en-GB"
                :date-format-options="{
                  year: 'numeric',
                  month: 'numeric',
                  day: 'numeric',
                }"
                disabled
              ></b-form-datepicker>
            </b-form-group>
            <b-form-group label="Deadline" label-for="deadline-input">
              <b-form-datepicker
                id="deadline-input"
                v-model="newProject.deadline"
                :min="min"
                :max="max"
                locale="en-GB"
                :date-format-options="{
                  year: 'numeric',
                  month: 'numeric',
                  day: 'numeric',
                }"
              ></b-form-datepicker>
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
              <b-form-input v-bind:value="(newProject.contact = contactDetails)" hidden></b-form-input>
            </b-form-group>
            <b-form-group
              label="Project handler"
              label-for="project-handler-input"
              invalid-feedback="Project Handler is required"
            >
              <b-form-select
                id="project-handler-input"
                v-model="newProject.project_handler"
                :options="handlerOptions"
                required
              ></b-form-select>
            </b-form-group>
            <b-form-group
              label="Quoted"
              label-for="quoted-input"
              invalid-feedback="Quotation is required"
            >
              <b-form-select
                id="quoted-input"
                v-model="newProject.quoted"
                :options="quotedOptions"
                required
              ></b-form-select>
            </b-form-group>
            <b-form-group label="Estimated completion" label-for="estimated-completion-input">
              <b-form-datepicker
                id="estimated-completion-input"
                v-model="newProject.estimated_completion"
                :min="min"
                :max="max"
                locale="en-GB"
                :date-format-options="{
                  year: 'numeric',
                  month: 'numeric',
                  day: 'numeric',
                }"
              ></b-form-datepicker>
            </b-form-group>
            <b-form-group
              label="Status"
              label-for="status-input"
              invalid-feedback="Status is required"
            >
              <b-form-select
                id="status-input"
                v-model="newProject.status"
                :options="statusOptions"
                required
              ></b-form-select>
            </b-form-group>
            <b-form-group label="Comments" label-for="comments-input">
              <b-form-textarea
                id="comments-input"
                v-model="newProject.comments"
                placeholder="Write a comment..."
                rows="3"
              ></b-form-textarea>
            </b-form-group>
            <b-button class="btn-info btn-block btn-lg" type="submit">Add Project</b-button>
          </b-form>
        </b-modal>
      </b-row>
      <!-- Edit Project Modal -->
      <b-row>
        <b-modal id="editModal" centered v-model="showEditModal" hide-footer>
          <template v-slot:modal-title>
            Edit Project
            <span
              class="subtitle"
              v-if="currentProject.last_change"
            >Last change by {{ currentProject.last_change }}</span>
            <span class="time">{{ currentProject.time_stamp }}</span>
          </template>
          <b-form @submit.prevent="updateProject">
            <b-form-group label="Type" label-for="type-input" invalid-feedback="Type is required">
              <b-form-select
                id="type-input"
                v-model="currentProject.type"
                :options="typeOptions"
                required
              ></b-form-select>
            </b-form-group>
            <b-form-group
              label="Title"
              label-for="title-input"
              invalid-feedback="Title is required"
            >
              <b-form-input id="title-input" v-model="currentProject.title" required></b-form-input>
            </b-form-group>
            <b-form-group
              label="Client"
              label-for="client-input"
              invalid-feedback="Client is required"
            >
              <b-form-select
                id="client-input"
                v-model="currentProject.client"
                :options="clientOptions"
                required
              ></b-form-select>
            </b-form-group>
            <b-form-group
              label="Description"
              label-for="description-input"
              invalid-feedback="Description is required"
            >
              <b-form-textarea
                id="description-input"
                v-model="currentProject.description"
                placeholder="Write a description..."
                rows="3"
                required
              ></b-form-textarea>
            </b-form-group>
            <b-form-group label="Start date" label-for="start-date-input">
              <b-form-datepicker
                id="start-date-input"
                v-model="currentProject.start_date"
                :min="min"
                :max="max"
                locale="en-GB"
                :date-format-options="{
                  year: 'numeric',
                  month: 'numeric',
                  day: 'numeric',
                }"
                disabled
              ></b-form-datepicker>
            </b-form-group>
            <b-form-group label="Deadline" label-for="deadline-input">
              <b-form-datepicker
                id="deadline-input"
                v-model="currentProject.deadline"
                :min="min"
                :max="max"
                locale="en-GB"
                :date-format-options="{
                  year: 'numeric',
                  month: 'numeric',
                  day: 'numeric',
                }"
              ></b-form-datepicker>
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
              <b-form-input v-bind:value="(currentProject.contact = contactDetails)" hidden></b-form-input>
            </b-form-group>
            <b-form-group
              label="Project Handler"
              label-for="project-handler-input"
              invalid-feedback="Project Handler is required"
            >
              <b-form-select
                id="project-handler-input"
                v-model="currentProject.project_handler"
                :options="handlerOptions"
                required
              ></b-form-select>
            </b-form-group>
            <b-form-group
              label="Quoted"
              label-for="quoted-input"
              invalid-feedback="Quotation is required"
            >
              <b-form-select
                id="quoted-input"
                v-model="currentProject.quoted"
                :options="quotedOptions"
                required
              ></b-form-select>
            </b-form-group>
            <b-form-group label="Estimated completion" label-for="estimated-completion-input">
              <b-form-datepicker
                id="estimated-completion-input"
                v-model="currentProject.estimated_completion"
                :min="min"
                :max="max"
                locale="en-GB"
                :date-format-options="{
                  year: 'numeric',
                  month: 'numeric',
                  day: 'numeric',
                }"
              ></b-form-datepicker>
            </b-form-group>
            <b-form-group
              label="Status"
              label-for="status-input"
              invalid-feedback="Status is required"
            >
              <b-form-select
                id="status-input"
                v-model="currentProject.status"
                :options="statusOptions"
                required
              ></b-form-select>
            </b-form-group>
            <b-form-group label="Comments" label-for="comments-input">
              <b-form-textarea
                id="comments-input"
                v-model="currentProject.comments"
                placeholder="Write a comment..."
                rows="3"
              ></b-form-textarea>
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
      <!-- Delete Project Modal -->
      <b-row>
        <b-modal
          id="deleteModal"
          centered
          v-model="showDeleteModal"
          title="Delete Project"
          hide-footer
        >
          <h4 class="text-danger">Are you sure you want to delete project?</h4>
          <hr />
          <button class="btn btn-danger btn-md" @click="deleteProject">Yes</button>
          &nbsp;&nbsp;&nbsp;&nbsp;
          <button
            class="btn btn-secondary btn-md"
            @click="showDeleteModal = false"
          >No</button>
        </b-modal>
      </b-row>
    </b-container>
  </div>
</template>

<script>
import API_BASE from "../api_base";
export default {
  name: "Projects",
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
          key: "type",
          label: "Type",
          visible: true,
          stickyColumn: true,
          sortable: true
        },
        {
          key: "title",
          label: "Title",
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
          key: "description",
          label: "Description",
          visible: true,
          stickyColumn: true,
          sortable: true, 
          class: "column-width"
        },
        {
          key: "start_date",
          label: "Start date",
          visible: true,
          stickyColumn: true,
          sortable: true
        },
        {
          key: "deadline",
          label: "Deadline",
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
          key: "project_handler",
          label: "Project handler",
          visible: true,
          stickyColumn: true,
          sortable: true
        },
        {
          key: "quoted",
          label: "Quoted",
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
          type: null,
          title: "",
          client: null,
          description: "",
          start_date: "",
          deadline: "",
          contact: "",
          project_handler: null,
          quoted: null,
          estimated_completion: "",
          status: null,
          last_change: "",
          comments: "",
          time_stamp: "",
          edit: "",
          delete: ""
        }
      ],
      typeOptions: [
        { value: null, text: "Please select type" },
        { value: "Hardware", text: "Hardware" },
        { value: "Hardware(Install)", text: "Hardware(Install)" },
        { value: "CGI", text: "CGI" },
        { value: "Interactive(Updates)", text: "Interactive(Updates)" },
        { value: "Interactive(New Project)", text: "Interactive(New Project)" },
        { value: "Interactive(New Phase)", text: "Interactive(New Phase)" },
        { value: "Internal", text: "Internal" }
      ],
      clientOptions: [{ value: null, text: "Please select client" }],
      handlerOptions: [
        { value: null, text: "Please select project handler" },
        { value: "Pedja", text: "Pedja" },
        { value: "Ine", text: "Ine" },
        { value: "David", text: "David" },
        { value: "Boki", text: "Boki" },
        { value: "Hadzi", text: "Hadzi" },
        { value: "Adam", text: "Adam" }
      ],
      quotedOptions: [
        { value: null, text: "Please select project quotation" },
        { value: "Awaiting info", text: "Awaiting info" },
        { value: "Quote sent", text: "Quote sent" },
        { value: "Approved no PO needed", text: "Approved no PO needed" },
        { value: "Approved awaiting PO", text: "Approved awaiting PO" },
        { value: "PO recieved", text: "PO recieved" },
        { value: "Invoiced", text: "Invoiced" }
      ],
      statusOptions: [
        { value: null, text: "Please select project status" },
        { value: "Pending approval", text: "Pending approval" },
        { value: "Awaiting materials", text: "Awaiting materials" },
        { value: "Materials recieved", text: "Materials recieved" },
        { value: "Production", text: "Production" },
        { value: "Update Live", text: "Update Live" },
        { value: "Completed", text: "Completed" },
        { value: "On hold", text: "On hold" }
      ],
      showAddModal: false,
      showEditModal: false,
      showDeleteModal: false,
      project_list: [],
      newProject: {
        type: null,
        title: "",
        client: null,
        description: "",
        start_date: new Date().toISOString().substring(0, 10),
        deadline: "",
        contact: "",
        project_handler: null,
        quoted: null,
        estimated_completion: "",
        status: null,
        comments: ""
      },
      currentProject: {}
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
    this.readProjectState();
    this.getAllProjects();
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
    },
    uncompletedProjects() {
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
    getAllProjects() {
      this.items = [];
      axios.get(`${API_BASE}/project_list.php?action=read`).then(response => {
        if (response.data.error) {
          this.errorMsg = true;
          this.errorMessage = response.data.message;
        } else {
          this.project_list = response.data.project_list;
          this.project_list.forEach(project => {
            if (project.description !== null) {
              project.description = project.description.replace(/\n/g, "<br>");
            }
            if (project.start_date === "0000-00-00" || project.start_date === "1000-01-01") {
              project.start_date = "";
            } else {
              project.start_date = this.parseISOToMyDate(project.start_date);
            }
            if (project.deadline === "0000-00-00" || project.deadline === "1000-01-01") {
              project.deadline = "";
            } else {
              project.deadline = this.parseISOToMyDate(project.deadline);
            }
            if (project.estimated_completion === "0000-00-00" || project.estimated_completion === "1000-01-01") {
              project.estimated_completion = "";
            } else {
              project.estimated_completion = this.parseISOToMyDate(
                project.estimated_completion
              );
            }
            if (project.comments !== null) {
              project.comments = project.comments.replace(/\n/g, "<br>");
            }
            this.items.push({
              id: project.id,
              type: project.type,
              title: project.title,
              client: project.client,
              description: project.description,
              start_date: project.start_date,
              deadline: project.deadline,
              contact: project.contact,
              project_handler: project.project_handler,
              quoted: project.quoted,
              estimated_completion: project.estimated_completion,
              status: project.status,
              last_change: project.last_change,
              comments: project.comments,
              time_stamp: project.time_stamp,
              edit: "<a class='text-success'><i class='fas fa-edit'></i></a>",
              delete: "<a class='text-danger'><i class='fas fa-trash'></i></a>"
            });
          });
        }
      });
    },
    addProject() {
      this.clearMsg();
      for (var i in this.newProject) {
        if (this.newProject[i] !== null) {
          this.newProject[i] = this.newProject[i].replace(/'+/g,"''");
        }
      }
      if (this.newProject.deadline === "") {
        this.newProject.deadline = "1000-01-01";
      }
      if (this.newProject.estimated_completion === "") {
        this.newProject.estimated_completion = "1000-01-01";
      }
      var formData = this.toFormData(this.newProject);      
      this.showAddModal = false;
      axios
        .post(`${API_BASE}/project_list.php?action=create`, formData)
        .then(response => {
          this.newProject = {
            type: null,
            title: "",
            client: null,
            description: "", 
            start_date: new Date().toISOString().substring(0, 10),
            deadline: "",
            contact: "",
            project_handler: null,
            quoted: null,
            estimated_completion: "",
            status: null,
            comments: ""
          };
          if (response.data.error) {
            this.errorMsg = true;
            this.errorMessage = response.data.message;
          } else {
            this.successMsg = true;
            this.successMessage = response.data.message;
            this.getAllProjects();
          }
        });
      this.name = "";
      this.phone = "";
      this.email = "";
    },
    updateProject() {
      this.clearMsg();
      for (var i in this.currentProject) {
        if (this.currentProject[i] !== null) {
          this.currentProject[i] = this.currentProject[i].replace(/'+/g,"''");
        }
      }
      if (this.currentProject.deadline === "NaN-NaN-NaN" || this.currentProject.deadline === "") {
        this.currentProject.deadline = "1000-01-01";
      } 
      if (this.currentProject.estimated_completion === "NaN-NaN-NaN" || this.currentProject.estimated_completion === "") {
        this.currentProject.estimated_completion = "1000-01-01";
      } 
      var formData = this.toFormData(this.currentProject);
      formData.append("last_change", this.$store.getters.accessToken.uidUsers);
      this.showEditModal = false;
      axios
        .post(`${API_BASE}/project_list.php?action=update`, formData)
        .then(response => {
          this.currentProject = {};
          if (response.data.error) {
            this.errorMsg = true;
            this.errorMessage = response.data.message;
          } else {
            this.successMsg = true;
            this.successMessage = response.data.message;
            this.getAllProjects();
          }
        });
      this.name = "";
      this.phone = "";
      this.email = "";
    },
    deleteProject() {
      this.clearMsg();
      var formData = this.toFormData(this.currentProject);
      this.showDeleteModal = false;
      this.showEditModal = false;
      axios
        .post(`${API_BASE}/project_list.php?action=delete`, formData)
        .then(response => {
          this.currentProject = {};
          if (response.data.error) {
            this.errorMsg = true;
            this.errorMessage = response.data.message;
          } else {
            this.successMsg = true;
            this.successMessage = response.data.message;
            this.getAllProjects();
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
    selectProject(project) {
      if (project.description !== null) {
        project.description = project.description.replace(/\n/g, "<br>");
      }
      if (project.comments !== null) {
        project.comments = project.comments.replace(/\n/g, "<br>");
      }
      if (project.time_stamp === "0000-00-00 00:00:00") {
        project.time_stamp = null;
      }  
      if (project.time_stamp !== null && project.time_stamp.indexOf("/") == -1) {
        project.time_stamp = project.time_stamp.split("-")[1] + "/" + project.time_stamp.slice(8, 10) + "/" + project.time_stamp.split("-")[0] + " " + project.time_stamp.slice(11);
      }   
      this.currentProject = {
        id: project.id,
        type: project.type,
        title: project.title,
        client: project.client,
        description: project.description,
        start_date: this.parseLocaleDateToISO(project.start_date),
        deadline: this.parseLocaleDateToISO(project.deadline),
        contact: project.contact,
        project_handler: project.project_handler,
        quoted: project.quoted,
        estimated_completion: this.parseLocaleDateToISO(
          project.estimated_completion
        ),
        status: project.status,
        last_change: project.last_change,
        comments: project.comments,
        time_stamp: this.parseISOToMyTime(project.time_stamp)
      };
      var str = this.currentProject.contact;
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
      if (
        key === "start_date" ||
        key === "deadline" ||
        key === "estimated_completion"
      ) {
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
      if (item.status === "Pending approval") {
        return "table-border-primary";
      } else if (item.status === "Awaiting materials") {
        return "table-border-info";
      } else if (
        item.status === "Materials recieved" &&
        (item.quoted === "Awaiting info" || item.quoted === "Quote sent")
      ) {
        return "table-border-info";
      } else if (
        item.status === "Materials recieved" &&
        (item.quoted === "Approved no PO needed" ||
          item.quoted === "Approved awaiting PO" ||
          item.quoted === "PO recieved" ||
          item.quoted === "Invoiced")
      ) {
        return "table-success";
      } else if (item.status === "Production") {
        return "table-border-warning";
      } else if (item.status === "Update Live") {
        return "table-border-orange";
      } else if (item.status === "Completed") {
        return "table-dark";
      } else if (item.status === "On hold") {
        return "table-border-danger";
      }
    },
    saveProjectsState() {
      var id = this.$store.getters.accessToken.id;
      var tableState = {
        id: this.fields[0].visible,
        type: this.fields[1].visible,
        title: this.fields[2].visible,
        client: this.fields[3].visible,
        description: this.fields[4].visible,
        start_date: this.fields[5].visible,
        deadline: this.fields[6].visible,
        contact: this.fields[7].visible,
        project_handler: this.fields[8].visible,
        quoted: this.fields[9].visible,
        estimated_completion: this.fields[10].visible,
        status: this.fields[11].visible,
        last_change: this.fields[12].visible,
        comments: this.fields[13].visible,
        edit: this.fields[14].visible,
        delete: this.fields[15].visible
      };
      var formData = new FormData();
      formData.append("id", id);
      formData.append("table_state", JSON.stringify(tableState));
      axios
        .post(`${API_BASE}/projects_state.php?action=update`, formData)
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
    readProjectState() {
      var id = this.$store.getters.accessToken.id;
      axios
        .get(`${API_BASE}/projects_state.php?action=read`, {
          params: {
            id: id
          }
        })
        .then(response => {
          if (response.data.error) {
            this.errorMsg = true;
            this.errorMessage = response.data.message;
          } else {
            if (response.data.projects_state) {
              var tableState = JSON.parse(response.data.projects_state);
              this.fields[0].visible = tableState.id;
              this.fields[1].visible = tableState.type;
              this.fields[2].visible = tableState.title;
              this.fields[3].visible = tableState.client;
              this.fields[4].visible = tableState.description;
              this.fields[5].visible = tableState.start_date;
              this.fields[6].visible = tableState.deadline;
              this.fields[7].visible = tableState.contact;
              this.fields[8].visible = tableState.project_handler;
              this.fields[9].visible = tableState.quoted;
              this.fields[10].visible = tableState.estimated_completion;
              this.fields[11].visible = tableState.status;
              this.fields[12].visible = tableState.last_change;
              this.fields[13].visible = tableState.comments;
              this.fields[14].visible = tableState.edit;
              this.fields[15].visible = tableState.delete;
            } else {
              this.defaultProjectState();
            }
          }
        });
    },
    defaultProjectState() {
      this.fields[15] = {
          key: "delete",
          label: "Delete",
          visible: true,
          stickyColumn: true,
          sortable: false
      };
      this.fields[0].visible = false;
      this.fields[1].visible = false;
      this.fields[3].visible = false;
      this.fields[4].visible = false;
      this.fields[5].visible = false;
      this.fields[7].visible = false;
      this.fields[10].visible = false;
      this.fields[12].visible = false;
      this.fields[14].visible = false;
      this.fields[15].visible = false;
    },
    onRowSelected(items) {
      if (items.length) {
        this.showEditModal = true;
        this.selectProject(items[0]);
        this.$refs.projectsTable.clearSelected();
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
.dropdown-menu li {
  min-width: 11rem !important;
}
.colours-legend {
  position: absolute;
  top: 80px;
  right: 21px;
  font-size: 12px;
}
.blue {
  background: #b8daff;
}
.light-blue {
  background: #bee5eb;
}
.green {
  background-color: #b1dfbb;
}
.yellow {
  background-color: #ffff66;
}
.dark-orange {
  background-color: #ffd966;
}
.grey {
  background-color: #b9bbbe;
}
.red {
  background-color: #f5c6cb;
}

@media screen and (max-width: 414px) {
  .phone-input {
    margin-bottom: 8px;
  }
}
</style>
