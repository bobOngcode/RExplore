<template>
  <div>
    <v-toolbar flat>
      <v-toolbar-title class="mt-2">Issued NTE</v-toolbar-title>
      <v-divider vertical class="ma-2 ml-4" thickness="20px"></v-divider>
      <template v-if="hasPermission('employee-master-data-nte-create')">
        <v-tooltip top v-if="!table_action_mode">
          <template v-slot:activator="{ on, attrs }">
            <v-btn 
              small 
              class="mx-2 mt-2" 
              color="primary" 
              rounded 
              fab
              v-bind="attrs" v-on="on"
              @click="newItem()" 
              :disabled="['Add', 'Edit'].includes(table_action_mode)"
            >
              <v-icon>mdi-plus</v-icon> 
            </v-btn>
          </template>
          <span>Add</span>
        </v-tooltip>
        <template v-if="['Add', 'Edit'].includes(table_action_mode)">
          <v-tooltip top >
            <template v-slot:activator="{ on, attrs }">
              <v-btn 
                small 
                class="mx-2 mt-2" 
                color="primary" 
                rounded 
                fab
                v-bind="attrs" v-on="on"
                @click="saveItem()" 
              >
                <v-icon>mdi-content-save</v-icon> 
              </v-btn>
            </template>
            <span>Save</span>
          </v-tooltip>
          <v-tooltip top >
            <template v-slot:activator="{ on, attrs }">
              <v-btn 
                small 
                class="mx-2 mt-2" 
                color="red" 
                rounded 
                fab
                v-bind="attrs" v-on="on"
                @click="resetData()" 
                dark
              >
                <v-icon>mdi-cancel</v-icon> 
              </v-btn>
            </template>
            <span>Cancel</span>
          </v-tooltip>
        </template>
      </template>
      
    </v-toolbar>
    <v-simple-table fixed-header class="tableFixHead" id="explanations" v-if="!table_action_mode">
      <template v-slot:default>
        <thead>
          <tr>
            <th class="pa-2" style="width:3%">#</th>
            <th class="pa-2" style="width:11%">Date Created</th>
            <th class="pa-2" style="width:11%">Date Issued</th>
            <th class="pa-2" style="width:11%">Issued By</th>
            <th class="pa-2" style="width:11%">NTE Code</th>
            <th class="pa-2" style="width:11%">Violation</th>
            <th class="pa-2" style="width:11%">Exp. Date</th>
            <th class="pa-2" style="width:11%">Remarks</th>
            <th class="pa-2" style="width:11%">Status</th>
            <th class="pa-2" style="width:6%">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in explanations">
            <td class="pa-2" style="width:3%"> {{ index + 1 }} </td>
            <td class="pa-2" style="width:11%"> {{ formatDate(item.create_date) }}</td>
            <td class="pa-2" style="width:11%"> {{ formatDate(item.date_issued) }}</td>
            <td class="pa-2" style="width:11%"> {{ item.issued_by }} </td>
            <td class="pa-2" style="width:11%"> {{ item.nte_code }} </td>
            <td class="pa-2 ellipsis" style="width:11%"> {{ item.violation }} </td>
            <td class="pa-2" style="width:11%"> {{ formatDate(item.explanation_date) }}</td>
            <td class="pa-2 ellipsis" style="width:11%"> {{ item.remarks }} </td>
            <td class="pa-2" style="width:11%"> {{ item.status }} </td>
            <td class="pa-2" style="width:6%">
              <v-icon
                small
                class="mr-2"
                color="green"
                @click="editItem(item)"
                v-if="hasPermission('employee-master-data-nte-edit')"
              >
                mdi-pencil
              </v-icon>

              <v-icon
                small
                color="red"
                @click="showConfirmAlert(item)"
                v-if="hasPermission('employee-master-data-nte-delete')"
              >
                mdi-delete
              </v-icon>
            </td>
          </tr>
        </tbody>
      </template>
    </v-simple-table>
    <div class="disciplinary-form px-4" v-if=" ['Add', 'Edit'].includes(table_action_mode)">
      <v-row class="mt-6">
        <v-col class="my-0 py-0">
          <v-text-field
            type="date"
            v-model="editedItem.create_date"
            label="Date Created"
            readonly
          ></v-text-field>
        </v-col>
        <v-col class="my-0 py-0">
          <v-text-field
            type="date"
            v-model="editedItem.date_issued"
            label="Date Issued"
            :error-messages="dateIssuedErrors"
            @input="$v.editedItem.date_issued.$touch() + validateDate('date_issued')"
            @blur="$v.editedItem.date_issued.$touch()"
          ></v-text-field>
        </v-col>
        <v-col class="my-0 py-0">
          <v-text-field
            v-model="editedItem.issued_by"
            label="Issued By"
            :error-messages="issuedByErrors"
            @input="$v.editedItem.issued_by.$touch()"
            @blur="$v.editedItem.issued_by.$touch()"
          ></v-text-field>
        </v-col>
        <v-col class="my-0 py-0">
          <v-text-field
            v-model="editedItem.nte_code"
            label="NTE Code"
            :error-messages="nteCodeErrors"
            @input="$v.editedItem.nte_code.$touch()"
            @blur="$v.editedItem.nte_code.$touch()"
          ></v-text-field>
        </v-col>
      </v-row>
      <v-row>
        <v-col class="my-0 py-0">
          <v-text-field
            v-model="editedItem.violation"
            label="Violation"
            :error-messages="violationErrors"
            @input="$v.editedItem.violation.$touch()"
            @blur="$v.editedItem.violation.$touch()"
          ></v-text-field>
        </v-col>
        <v-col class="my-0 py-0">
          <v-text-field
            type="date"
            v-model="editedItem.explanation_date"
            label="Explanation Date"
            :error-messages="explanationDateErrors"
            @input="validateDate('explanation_date')"
          ></v-text-field>
        </v-col>
        <v-col class="my-0 py-0">
          <v-text-field
            v-model="editedItem.remarks"
            label="Remarks"
          ></v-text-field>
        </v-col>
        <v-col class="my-0 py-0">
          <v-autocomplete
            v-model="editedItem.status"
            :items="['Open', 'Closed']"
            item-text="text"
            item-value="value"
            label="Status"
          >
          </v-autocomplete>
        </v-col>
      </v-row>
      <v-row>
        <v-col :class="'my-0 py-0 ' + (this.editedItem.nte_file_name ? 'pt-5' : '')">
          <template v-if="this.editedItem.nte_file_name">
            <span class="subtitle-1 mt-4">File: </span>
            <span> 
              <v-btn 
                class="mb-1"
                small 
                color="primary" 
                text 
                @click="hasAnyPermission('employee-master-data-nte-file-download') ? downloadFile('nte_file') : ''"
              > 
                <!-- {{ editedItem.nte_file_name }}  -->
                NTE File
              </v-btn> 
            </span>
            <v-tooltip top>
              <template v-slot:activator="{ on, attrs }">
                <v-btn 
                  class="mb-1"
                  x-small
                  color="error" 
                  rounded
                  icon
                  @click="confirmDeleteFile('nte_file')"
                  v-if="hasAnyPermission('employee-master-data-nte-file-delete')"
                  v-bind="attrs" 
                  v-on="on"
                > 
                  <v-icon>mdi-delete</v-icon> 
                </v-btn>
              </template>
              <span>Delete File</span>
            </v-tooltip> 
          </template>
          <template v-if="!this.editedItem.nte_file_name">
            <v-file-input
              v-model="nte_file_input"
              show-size
              label="NTE File (Attachment)"
              prepend-icon="mdi-paperclip"
              required
              :error-messages="nteFileErrors"
              @change="validateFile('nte_file_input')"
              @input="$v.nte_file_input.$touch()"
              @blur="$v.nte_file_input.$touch()"
              clearable
            >
            </v-file-input>
          </template>
        </v-col>
        <v-col class="my-0 py-0 ">
          <v-text-field
            type="date"
            v-model="editedItem.nte_date_upload"
            label="NTE Date Upload"
            readonly
          ></v-text-field>
        </v-col>
        <v-col :class="'my-0 py-0 ' + (this.editedItem.explanation_file_name ? 'pt-5' : '')">
          <template v-if="this.editedItem.explanation_file_name">
            <span class="subtitle-1 mt-4">File: </span>
            <span> 
              <v-btn 
                class="mb-1"
                small 
                color="primary" 
                text 
                v-if="hasAnyPermission('employee-master-data-nte-file-download')"
                @click="hasAnyPermission('employee-master-data-nte-file-download') ? downloadFile('explanation_file') : ''"
              > 
                <!-- {{ editedItem.explanation_file_name }}  -->
                Explanation File
              </v-btn> 
            </span>
            <v-tooltip top>
              <template v-slot:activator="{ on, attrs }">
                <v-btn 
                  class="mb-1"
                  x-small
                  color="error" 
                  rounded
                  icon
                  @click="confirmDeleteFile('explanation_file')"
                  v-if="hasAnyPermission('employee-master-data-nte-file-delete')"
                  v-bind="attrs" 
                  v-on="on"
                > 
                  <v-icon>mdi-delete</v-icon> 
                </v-btn>
              </template>
              <span>Delete File</span>
            </v-tooltip> 
          </template>
          <template v-if="!this.editedItem.explanation_file_name">
            <v-file-input
              v-model="explanation_file_input"
              show-size
              label="Explanation File (Attachment)"
              prepend-icon="mdi-paperclip"
              required
              :error-messages="explanationFileErrors"
              @change="validateFile('explanation_file_input')"
              clearable
            >
            </v-file-input>
          </template>
        </v-col>
        <v-col class="my-0 py-0 ">
          <v-text-field
            type="date"
            v-model="editedItem.explanation_date_upload"
            label="Explanation Date Upload"
            readonly
          ></v-text-field>
        </v-col>
      </v-row>
    </div>
  </div>
</template>
<style scoped>
  .full-height {
    height: calc(85vh - 130px); /* Adjust 270px to suits your needs */
    overflow-y: auto;
    overflow-x: hidden;
  }

  table {
    width: 100%;
  }

  .disciplinary-form {
    height: calc(70vh - 135px);
    overflow-y: auto;
    overflow-x: hidden;
  }

  thead, tbody, tr, td, th { display: block; }

  tr:after {
      content: ' ';
      display: block;
      visibility: hidden;
      clear: both;
  }

  tbody {
      height: calc(65vh - 135px);
      overflow-y: auto;
  }

  tbody td, thead th {
      float: left;
  }

  .ellipsis { 
    position: relative; 
  } 

  .ellipsis:before { 
    content: ' '; 
    visibility: hidden; 
  } 

  .ellipsis { 
    /* position: absolute;  */
    left: 0; 
    right: 0; 
    white-space: nowrap; 
    overflow: hidden; 
    text-overflow: ellipsis; 
  } 

</style>
<script>

import axios from "axios";
import { validationMixin } from "vuelidate";
import { required, requiredIf, email } from "vuelidate/lib/validators";
import { mapGetters } from "vuex";
import LoginVue from '../../../../../auth/Login.vue';

export default {

  props: ['editedIndex', 'data'],

  mixins: [validationMixin],

  validations: {
    editedItem: { 
      date_issued: { required: requiredIf(function () {
          return this.table_action_mode;
        }),  
      },
      issued_by: { required: requiredIf(function () {
          return this.table_action_mode;
        }),  
      },
      nte_code: { required: requiredIf(function () {
          return this.table_action_mode;
        }),  
      },
      violation: { required: requiredIf(function () {
          return this.table_action_mode;
        }),  
      },
      
    },
    nte_file_input: {  required: requiredIf(function () {
        return this.table_action_mode;
      }),  
    },
    // explanation_file_input: { required },
  },
  data() {
    return {
      editedExplanationIndex: -1,
      editedItem: {
        created_date: "",
        date_issued: "",
        issued_by: "",
        nte_code: "",
        nte_file: "",
        nte_date_upload: "",
        violation: "",
        explanation_file: "",
        explanation_date: "",
        explanation_date_upload: "",
        remarks: "",
        status: "Open",
      },
      defaultField: {
        created_date: "",
        date_issued: "",
        issued_by: "",
        nte_code: "",
        nte_file: "",
        nte_date_upload: "",
        violation: "",
        explanation_file: "",
        explanation_date: "",
        explanation_date_upload: "",
        remarks: "",
        status: "Open",
      },
      explanations: [],
      added_explanations: [],
      deleted_explanations: [],
      action_mode: "",
      table_action_mode: "",
      disabled: false,
      addedItems: [],
      deletedItems: [],
      dateErrors: {
        date_issued: { status: false, msg: "" },
        explanation_date: { status: false, msg: "" },
      },
      nte_file_input: [],
      explanation_file_input: [],
      fileInvalid: false,
    };
  },

  methods: {
    newItem() {
      this.resetData();
      this.table_action_mode = "Add";
    },
    saveExplanation() {

      const formData = new FormData();
      let remarks = this.editedItem.remarks ? this.editedItem.remarks : '';
      let explanation_date = this.editedItem.explanation_date ? this.editedItem.explanation_date : '';
      formData.append('employee_id', this.data.id);
      formData.append('date_issued', this.editedItem.date_issued);
      formData.append('issued_by', this.editedItem.issued_by);
      formData.append('nte_code', this.editedItem.nte_code);
      formData.append('nte_file', this.nte_file_input);
      formData.append('violation', this.editedItem.violation);
      formData.append('explanation_file', this.explanation_file_input);
      formData.append('explanation_date', explanation_date);
      formData.append('remarks', remarks);
      formData.append('status', this.editedItem.status);

      let api = this.editedExplanationIndex > -1 ? 'update/'+this.editedItem.id : 'store'
      
      axios.post("/api/employee_master_data/nte/" + api, formData, {
        headers: {
          Authorization: "Bearer " + localStorage.getItem("access_token"),
          "Content-Type": "multipart/form-data",
        }
      }).then(
        (response) => {
          this.loading = false;
          let data = response.data;

          if(data.success)
          {
            this.explanations = data.explanations;
            this.$emit('updateIssuedNTE', data.explanations);
            this.showAlert(data.success);
          }
          
          // reset array
          this.added_explanations = [];
        },
        (error) => {
          this.isUnauthorized(error);
        }
      );
    },

    deleteExplanation(item) {
      
      this.loading = true;
      let data = { explanation_id: item.id, employee_id: this.data.id };
      axios.post("/api/employee_master_data/nte/delete", data).then(
        (response) => {
          this.loading = false;

          let data = response.data;
          if(data.success)
          {
            this.$emit('updateIssuedNTE', data.explanations);
            this.showAlert(data.success);
            this.explanations = data.explanations
            // this.removeItem();
          }
          else
          {
            this.showErrorAlert(data.error);
          }
        },
        (error) => {
          this.isUnauthorized(error);
        }
      );
    },

    saveItem(){
 
      this.$v.$touch();
      let dateModelHasErrors = Object.values(this.dateErrors).map((obj) => obj.status).includes(true);
      
      if(!this.$v.editedItem.$error && !dateModelHasErrors && !this.fileInvalid)
      {
        
        // edit mode; from parent component
        if(this.editedIndex > -1)
        {
          this.saveExplanation();
        }

        Object.assign(this.editedItem, { nte_file: this.nte_file_input, explanation_file: this.explanation_file_input });

        if(this.table_action_mode === 'Add')
        {
          this.explanations.push(this.editedItem);
        }
        else
        {
          this.explanations[this.editedExplanationIndex] = this.editedItem;
        }

        this.resetData();
      }
      
    },

    cancelEvent(item) {
      this.resetData();
    },

    editItem(item) {
      this.table_action_mode = "Edit";
      this.editedItem = Object.assign({}, item);
      this.editedExplanationIndex = this.explanations.indexOf(item);

      // add mode; from parent component
      if(this.editedIndex == -1)
      {
        this.nte_file_input = item.nte_file;
        this.explanation_file_input = item.explanation_file;
      }
      
    },

    resetData(){
      this.$v.editedItem.$reset();
      this.editedItem = Object.assign({}, this.defaultField);
      this.editedExplanationIndex = -1;
      this.table_action_mode = "";
      this.nte_file_input = [];
      this.explanation_file_input = [];
    },

    clear() {
      this.resetData();
      this.explanations = [];
      this.added_explanations = [];
      this.deleted_explanations = [];
      this.addedItems = [];
      this.deletedItems = [];

    },

    showAlert(msg) {
      this.$swal({
        position: "center",
        icon: "success",
        title: msg,
        showConfirmButton: false,
        timer: 2500,
      });
    },

    showErrorAlert(msg) {     
      this.$swal({
        position: "center",
        icon: "error",
        title: msg,
        showConfirmButton: false,
        timer: 2500,
      });
    },

    showConfirmAlert(item) {

      this.editedItem = Object.assign({}, item);
   
      if(this.editedIndex > -1)
      {
        this.$swal({
          title: "Are you sure?",
          text: "You won't be able to revert this!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#d33",
          cancelButtonColor: "#6c757d",
          confirmButtonText: "Delete record!",
        }).then((result) => {
          // <--

          if (result.value) {
            
            this.deleteExplanation(item);
            
          }
        });
      }
      else
      {
        this.removeItem();
      }

      
    },

    removeItem() {
      let index = this.explanations.indexOf(this.editedItem);
      this.explanations.splice(index, 1);
    },

    validateDate(field) {
      
      // if field is set for validation
      if(this.$v.editedItem[field])
      {
        this.$v.editedItem[field].$touch();
      }

      let min_date = new Date('1900-01-01').getTime();
      let max_date = new Date().getTime();
      let date = this.editedItem[field];
   
      if(date)
      {
        let date_value = new Date(date).getTime();
        let [year, month, day] = date.split('-');

        this.dateErrors[field].status = false;
        this.dateErrors[field].msg = "";

        // if (date_value < min_date || date_value > max_date || year.length > 4) {
        if (date_value > max_date || year.length > 4 || year < 1900) {
          this.dateErrors[field].status = true;
          this.dateErrors[field].msg = 'Enter a valid date';
        }  
      }
  
    },

    formatDate(date) {
      if (!date) return null;
      const [year, month, day] = date.split("-");
      return `${month}/${day}/${year}`;
    },

    validateFile(file){
      let myFileInput = this[file];

      let extensions = ['docs', 'docx', 'pdf', 'jpg', 'jpeg', 'png'];
    
      if(myFileInput)
      {
        if(myFileInput.name)
        {
          let split_arr = myFileInput.name.split('.');
          let split_ctr = split_arr.length;
          let extension = split_arr[split_ctr - 1].toLowerCase();
          
          if(!extensions.includes(extension))
          {
            this.fileInvalid = true;
          }

          if(myFileInput.size > 5000000) // 5000000 bytes or 20MB
          {
            this.fileInvalid = true;
          }
        }
      }

    },

    confirmDeleteFile(document_type) {
     
      if(this.editedIndex > -1)
      {

        this.$swal({
          title: "Are you sure?",
          text: "You won't be able to revert this!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#d33",
          cancelButtonColor: "#6c757d",
          confirmButtonText: "Delete File!",
        }).then((result) => {
          if (result.value) {

            const data = { explanation_id: this.editedItem.id, document_type: document_type }
            axios.post("/api/employee_master_data/nte/file_delete", data).then(
              (response) => {
                let data = response.data;
                console.log(data);
                
                if (data.success) {
                  this.showAlert(data.success, 'success');
                  this.$emit('updateIssuedNTE', data.explanations);

                  let explanation = data.explanations.filter((value) => {
                    return value.id == this.editedItem.id;
                  })

                  this.editedItem = Object.assign({}, explanation[0])
                  
                  this.explanations[this.editedExplanationIndex] = explanation[0];

                }
                this.loading = false;
              },
              (error) => {
                this.isUnauthorized(error);
              }
            );
          }
        });
      }
      else
      {

        let i = this.employee_files.indexOf(item);

        this.removedFiles.push(item);

        this.employee_files.splice(i, 1);
      }
    },

    downloadFile(document_type) {

      let title = document_type == 'nte_file' ? this.editedItem.nte_file_name : this.editedItem.explanation_file_name;
      let file_type = document_type == 'nte_file' ? this.editedItem.nte_file_type : this.editedItem.explanation_file_type;
      const data = { explanation_id: this.editedItem.id, document_type: document_type }

      axios.post('/api/employee_master_data/nte/file_download', data, { responseType: 'arraybuffer'})
          .then((response) => {
            var fileURL = window.URL.createObjectURL(new Blob([response.data]));
            var fileLink = document.createElement('a');
            fileLink.href = fileURL;
            fileLink.setAttribute('download', title);
            document.body.appendChild(fileLink);
            fileLink.click();
        }, (error) => {
          console.log(error);
        }
      );
    },

  },
  computed: {
    dateIssuedErrors() {
      const errors = [];
      if (!this.$v.editedItem.date_issued.$dirty) return errors;
      !this.$v.editedItem.date_issued.required && errors.push("Date Assigned is required.");

      if(this.dateErrors.date_issued.msg)
      {
        errors.push(this.dateErrors.date_issued.msg);
      }

      return errors;
    },

    issuedByErrors() {
      const errors = [];
      if (!this.$v.editedItem.issued_by.$dirty) return errors;
      !this.$v.editedItem.issued_by.required &&
        errors.push("Issued By is required.");
      return errors;
    },

    nteCodeErrors() {
      const errors = [];
      if (!this.$v.editedItem.nte_code.$dirty) return errors;
      !this.$v.editedItem.nte_code.required &&
        errors.push("NTE Code is required.");
      return errors;
    },

    nteFileErrors() {
      
      const errors = [];

      let file = this.nte_file_input;
      let extensions = ['docs', 'docx', 'pdf', 'jpg', 'jpeg', 'png'];
      let errorMsg = "";
      let fileInvalid = false;
    
      if(file)
      {
        if(file.name)
        {
          let split_arr = file.name.split('.');
          let split_ctr = split_arr.length;
          let extension = split_arr[split_ctr - 1].toLowerCase();
          
          if(!extensions.includes(extension))
          {
            fileInvalid = true;
            errorMsg = `File type must be ${extensions.join(', ')}.`;
          }

          if(file.size > 5000000) // 5000000 bytes or 20MB
          {
            errorMsg = "File size maximum is 5MB";
            fileInvalid = true;
          }
        }
      }
      this.fileInvalid = fileInvalid;
      fileInvalid && errors.push(errorMsg);

      return errors
        
    },

    violationErrors() {
      const errors = [];
      if (!this.$v.editedItem.violation.$dirty) return errors;
      !this.$v.editedItem.violation.required &&
        errors.push("Violation is required.");
      return errors;
    },

    explanationFileErrors() {
      
      const errors = [];

      let file = this.explanation_file_input;
      let extensions = ['docs', 'docx', 'pdf', 'jpg', 'jpeg', 'png'];
      let errorMsg = "";
      let fileInvalid = false;
    
      if(file)
      {
        if(file.name)
        {
          let split_arr = file.name.split('.');
          let split_ctr = split_arr.length;
          let extension = split_arr[split_ctr - 1].toLowerCase();
          
          if(!extensions.includes(extension))
          {
            fileInvalid = true;
            errorMsg = `File type must be ${extensions.join(', ')}.`;
          }

          if(file.size > 5000000) // 5000000 bytes or 20MB
          {
            errorMsg = "File size maximum is 5MB";
            fileInvalid = true;
          }
        }
      }
      this.fileInvalid = fileInvalid;
      fileInvalid && errors.push(errorMsg);

      return errors
        
    },

    explanationDateErrors() {
      const errors = [];
      // if (!this.$v.editedItem.date_issued.$dirty) return errors;
      // !this.$v.editedItem.date_issued.required && errors.push("Date Assigned is required.");

      if(this.dateErrors.explanation_date.msg)
      {
        errors.push(this.dateErrors.explanation_date.msg);
      }

      return errors;
    },

    remarksErrors() {
      const errors = [];
      if (!this.$v.editedItem.remarks.$dirty) return errors;
      !this.$v.editedItem.remarks.required &&
        errors.push("Remarks is required.");
      return errors;
    },

    ...mapGetters("auth", ["isUnauthorized"]),
    ...mapGetters("userRolesPermissions", ["hasRole", "hasAnyRole", "hasPermission", "hasAnyPermission"]),

  },

  mounted() {
    axios.defaults.headers.common["Authorization"] =
      "Bearer " + localStorage.getItem("access_token");
    
    if(this.editedIndex > -1)
    {
      this.explanations = this.data.explanations;
    }
  },
};
</script>
