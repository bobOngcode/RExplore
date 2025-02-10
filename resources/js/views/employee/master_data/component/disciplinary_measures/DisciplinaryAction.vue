<template>
  <div>
    <v-toolbar flat>
      <v-toolbar-title class="mt-2">Disciplinary Action</v-toolbar-title>
      <v-divider vertical class="ma-2 ml-4" thickness="20px"></v-divider>
      <template v-if="hasPermission('employee-master-data-disciplinary-create')">
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
    <v-simple-table fixed-header class="tableFixHead" id="disciplinaries" v-if="!table_action_mode">
      <template v-slot:default>
        <thead>
          <tr>
            <th class="pa-2" style="width:2%">#</th>
            <th class="pa-2" style="width:9%">Date Created</th>
            <th class="pa-2" style="width:9%">Date Issued</th>
            <th class="pa-2" style="width:11%">NTE Code</th>
            <th class="pa-2" style="width:11%">Offense Code</th>
            <th class="pa-2" style="width:11%">Offense</th>
            <th class="pa-2" style="width:11%">Type</th>
            <th class="pa-2" style="width:11%">Disc. Action</th>
            <th class="pa-2" style="width:11%">Series</th>
            <th class="pa-2" style="width:8%">Status</th>
            <th class="pa-2" style="width:6%">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in disciplinaries">
            <td class="pa-2" style="width:2%"> {{ index + 1 }} </td>
            <td class="pa-2" style="width:9%"> {{ formatDate(item.create_date) }}</td>
            <td class="pa-2" style="width:9%"> {{ formatDate(item.date_issued) }}</td>
            <td class="pa-2" style="width:11%"> {{ item.nte_code }} </td>
            <td class="pa-2" style="width:11%"> {{ item.offense_code }} </td>
            <td class="pa-2 ellipsis" style="width:11%"> {{ item.offense }} </td>
            <td class="pa-2" style="width:11%"> {{ item.offense_type }} </td>
            <td class="pa-2" style="width:11%"> {{ item.disciplinary_action }} </td>
            <td class="pa-2" style="width:11%"> {{ item.series }} </td>
            <td class="pa-2" style="width:8%"> {{ item.status }} </td>
            <td class="pa-2" style="width:6%">
              <v-icon
                small
                class="mr-2"
                color="green"
                @click="editItem(item)"
                v-if="hasPermission('employee-master-data-disciplinary-edit')"
              >
                mdi-pencil
              </v-icon>

              <v-icon
                small
                color="red"
                @click="showConfirmAlert(item)"
                v-if="hasPermission('employee-master-data-disciplinary-delete')"
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
            v-model="editedItem.nte_code"
            label="NTE Code"
            :error-messages="nteCodeErrors"
            @input="$v.editedItem.nte_code.$touch()"
            @blur="$v.editedItem.nte_code.$touch()"
          ></v-text-field>
        </v-col>
         <v-col class="my-0 py-0">
          <v-text-field
            v-model="editedItem.offense_code"
            label="Offense Code"
            :error-messages="offenseCodeErrors"
            @input="$v.editedItem.offense_code.$touch()"
            @blur="$v.editedItem.offense_code.$touch()"
          ></v-text-field>
        </v-col>
      </v-row>
      <v-row>
        <v-col class="my-0 py-0">
          <v-autocomplete
            v-model="editedItem.offense"
            label="Offense"
            :items="offenses"
            :error-messages="offenseErrors"
            @input="$v.editedItem.offense.$touch()"
            @blur="$v.editedItem.offense.$touch()"
          ></v-autocomplete>
        </v-col>
        <v-col class="my-0 py-0">
          <v-text-field
            v-model="editedItem.offense_type"
            label="Offense Type"
            :error-messages="offenseTypeErrors"
            @input="$v.editedItem.offense_type.$touch()"
            @blur="$v.editedItem.offense_type.$touch()"
          ></v-text-field>
        </v-col>
        <v-col class="my-0 py-0">
          <v-autocomplete
            v-model="editedItem.disciplinary_action"
            label="Disciplinary Action"
            :items="disciplinary_measures"
            :error-messages="disciplinaryActionErrors"
            @input="$v.editedItem.disciplinary_action.$touch()"
            @blur="$v.editedItem.disciplinary_action.$touch()"
          ></v-autocomplete>
        </v-col>
        <v-col class="my-0 py-0">
          <v-autocomplete
            v-model="editedItem.series"
            label="Series of Disciplinary"
            :items="offense_series"
            :error-messages="seriesErrors"
            @input="$v.editedItem.series.$touch()"
            @blur="$v.editedItem.series.$touch()"
          ></v-autocomplete>
        </v-col>
      </v-row>
      <v-row>
        <v-col class="my-0 py-0">
          <v-text-field
            type="date"
            v-model="editedItem.transmit_date"
            label="Transmit Date"
            :error-messages="transmitDateErrors"
            @input="validateDate('transmit_date')"
          ></v-text-field>
        </v-col>
        <v-col class="my-0 py-0">
          <v-text-field
            type="date"
            v-model="editedItem.return_date"
            label="Return Date"
            :error-messages="returnDateErrors"
            @input="validateDate('return_date')"
          ></v-text-field>
        </v-col>
        <v-col :class="'my-0 py-0 ' + (this.editedItem.file_name ? 'pt-5' : '')">
          <template v-if="this.editedItem.file_name">
            <span class="subtitle-1 mt-4">File: </span>
            <span> 
              <v-btn 
                class="mb-1"
                small 
                color="primary" 
                text 
                @click="hasAnyPermission('employee-master-data-disciplinary-file-download') ? downloadFile() : ''"
              > 
                <!-- {{ editedItem.file_name }}  -->
                Memo File
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
                  @click="confirmDeleteFile()"
                  v-if="hasAnyPermission('employee-master-data-disciplinary-file-delete')"
                  v-bind="attrs" 
                  v-on="on"
                > 
                  <v-icon>mdi-delete</v-icon> 
                </v-btn>
              </template>
              <span>Delete File</span>
            </v-tooltip> 
          </template>
          <template v-if="!this.editedItem.file_name">
            <v-file-input
              v-model="file_input"
              show-size
              label="Memo File (Attachment)"
              prepend-icon="mdi-paperclip"
              required
              :error-messages="fileErrors"
              @change="validateFile('file_input')"
              @input="$v.file_input.$touch()"
              @blur="$v.file_input.$touch()"
              clearable
            >
            </v-file-input>
          </template>
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

export default {

  props: ['editedIndex', 'data'],

  mixins: [validationMixin],

  validations: {
    editedItem: { 
      date_issued: { required: requiredIf(function () {
          return this.table_action_mode;
        }),  
      },
      nte_code: { required: requiredIf(function () {
          return this.table_action_mode;
        }),  
      },
      offense_code: { required: requiredIf(function () {
          return this.table_action_mode;
        }),  
      },
      offense: { required: requiredIf(function () {
          return this.table_action_mode;
        }),  
      },
      offense_type: { required: requiredIf(function () {
          return this.table_action_mode;
        }),  
      },
      disciplinary_action: { required: requiredIf(function () {
          return this.table_action_mode;
        }),  
      },
      series: { required: requiredIf(function () {
          return this.table_action_mode;
        }),  
      },
      
    },
    file_input: {  required: requiredIf(function () {
        return this.table_action_mode;
      }),  
    },
    // explanation_file_input: { required },
  },
  data() {
    return {
      editedDisciplinaryIndex: -1,
      editedItem: {
        create_date: "",
        date_issued: "",
        nte_code: "",
        offense_code: "",
        offense: "",
        offense_type: "",
        disciplinary_action: "",
        file: "",
        series: "",
        transmit_date: "",
        return_date: "",
        status: "Open",
      },
      defaultField: {
        create_date: "",
        date_issued: "",
        nte_code: "",
        offense_code: "",
        offense: "",
        offense_type: "",
        disciplinary_action: "",
        file: "",
        series: "",
        transmit_date: "",
        return_date: "",
        status: "Open",
      },
      disciplinaries: [],
      added_disciplinaries: [],
      deleted_disciplinaries: [],
      action_mode: "",
      table_action_mode: "",
      disabled: false,
      addedItems: [],
      deletedItems: [],
      dateErrors: {
        date_issued: { status: false, msg: "" },
        explanation_date: { status: false, msg: "" },
        transmit_date: { status: false, msg: "" },
        return_date: { status: false, msg: "" },
      },
      file_input: [],
      fileInvalid: false,
      offenses: [
        'TIMEKEEPING OFFENSE',
        'OFFENSES RELATED TO JOB PERFORMANCE',
        'OFFENSES RELATED TO CONDUCT & BEHAVIOUR',
        'OFFENSE AGAINST PROPERTY',
        'OFFENSES RELATED TO SECURITY',
        'OFFENSE AGAINST HEALTH & SAFETY',
        'OFFENSES AGAINST ATTENDANCE',
        'OFFENSES RELATED TO VEHICLE MAINTENANCE'
      ],
      disciplinary_measures: [
        'Verbal Warning',
        'Written Warning',
        'Last & Final Warning',
        'Suspension',
        'Preventive Suspension',
        'Dismissal/Termination'
      ],
      offense_series: [
        'First Offense',
        'Second Offense',
        'Third Offense',
        'Fourth Offense',
        'Fifth Offense',
      ]
    };
  },

  methods: {
    newItem() {
      this.resetData();
      this.table_action_mode = "Add";

      // let hasNew = false;
      
      // this.disciplinaries.forEach((value, index) => {
      //   if (value.status === "New") {
      //     hasNew = true;
      //   }
      // });

      // if (!hasNew) {
      //   this.disciplinaries.push({ status: "New" });
      // }
      
      // setTimeout(() => {
      //   let container = this.$el.querySelector("#disciplinaries tbody");
      //   container.scrollTop = container.scrollHeight;
      // }, 1);

    },
    saveDisciplinary() {

      // let data = Object.assign(this.editedItem, { 
      //   employee_id: this.data.id,  
      //   nte_file: this.file_input,
      //   explanation_file: this.explanation_file_input,
      // });
      
      const formData = new FormData();

      formData.append('employee_id', this.data.id);
      formData.append('date_issued', this.editedItem.date_issued);
      formData.append('nte_code', this.editedItem.nte_code);
      formData.append('offense_code', this.editedItem.offense_code);
      formData.append('offense', this.editedItem.offense);
      formData.append('offense_type', this.editedItem.offense_type);
      formData.append('disciplinary_action', this.editedItem.disciplinary_action);
      formData.append('file', this.file_input);
      formData.append('series', this.editedItem.series);
      formData.append('status', this.editedItem.status);


      let api = this.editedDisciplinaryIndex > -1 ? 'update/'+this.editedItem.id : 'store'
      
      axios.post("/api/employee_master_data/disciplinary/" + api, formData, {
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
            this.disciplinaries = data.disciplinaries;
            this.$emit('updateDisciplinaryAction', data.disciplinaries);
            this.showAlert(data.success);
          }
          
          // reset array
          this.added_disciplinaries = [];
        },
        (error) => {
          this.isUnauthorized(error);
        }
      );
    },

    deleteExplanation(item) {
      
      this.loading = true;
      let data = { disciplinary_id: item.id, employee_id: this.data.id };
      axios.post("/api/employee_master_data/disciplinary/delete", data).then(
        (response) => {
          this.loading = false;

          let data = response.data;
          if(data.success)
          {
            this.$emit('updateDisciplinaryAction', data.disciplinaries);
            this.showAlert(data.success);
            this.disciplinaries = data.disciplinaries
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

        // if edit mode from parent component
        if(this.editedIndex > -1)
        {
          this.saveDisciplinary();
        }

        Object.assign(this.editedItem, { file: this.file_input });

        // if table action mode is Add then push item/data
        if(this.table_action_mode === 'Add')
        {
          this.disciplinaries.push(this.editedItem);
        }
        else // if table action mode is Edit then assign item/data
        {
          this.disciplinaries[this.editedDisciplinaryIndex] = this.editedItem;
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
      this.editedDisciplinaryIndex = this.disciplinaries.indexOf(item);

      // add mode; from parent component
      if(this.editedIndex == -1)
      {
        this.file_input = item.nte_file;
        this.explanation_file_input = item.explanation_file;
      }
      
    },

    resetData(){
      this.$v.$reset();
      this.editedItem = Object.assign({}, this.defaultField);
      this.editedDisciplinaryIndex = -1;
      this.table_action_mode = "";
      this.file_input = [];
      this.explanation_file_input = [];
    },

    clear() {
      this.resetData();
      this.disciplinaries = [];
      this.added_disciplinaries = [];
      this.deleted_disciplinaries = [];
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
      let index = this.disciplinaries.indexOf(this.editedItem);
      this.disciplinaries.splice(index, 1);
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

    confirmDeleteFile() {
     
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

            const data = { disciplinary_id: this.editedItem.id }
            axios.post("/api/employee_master_data/disciplinary/file_delete", data).then(
              (response) => {
                let data = response.data;
                console.log(data);
                
                if (data.success) {
                  this.showAlert(data.success, 'success');
                  this.$emit('updateDisciplinaryAction', data.disciplinaries);

                  let disciplinaries = data.disciplinaries.filter((value) => {
                    return value.id == this.editedItem.id;
                  })

                  this.editedItem = Object.assign({}, disciplinaries[0])
                  
                  this.disciplinaries[this.editedDisciplinaryIndex] = disciplinaries[0];

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

    downloadFile() {

      let title = this.editedItem.file_name;
      const data = { disciplinary_id: this.editedItem.id }

      axios.post('/api/employee_master_data/disciplinary/file_download', data, { responseType: 'arraybuffer'})
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

    nteCodeErrors() {
      const errors = [];
      if (!this.$v.editedItem.nte_code.$dirty) return errors;
      !this.$v.editedItem.nte_code.required &&
        errors.push("NTE Code is required.");
      return errors;
    },
    offenseCodeErrors() {
      const errors = [];
      if (!this.$v.editedItem.offense_code.$dirty) return errors;
      !this.$v.editedItem.offense_code.required &&
        errors.push("Offense Code is required.");
      return errors;
    },
    offenseErrors() {
      const errors = [];
      if (!this.$v.editedItem.offense.$dirty) return errors;
      !this.$v.editedItem.offense.required &&
        errors.push("Offense is required.");
      return errors;
    },
    offenseTypeErrors() {
      const errors = [];
      if (!this.$v.editedItem.offense_type.$dirty) return errors;
      !this.$v.editedItem.offense_type.required &&
        errors.push("Offense Type is required.");
      return errors;
    },
    disciplinaryActionErrors() {
      const errors = [];
      if (!this.$v.editedItem.disciplinary_action.$dirty) return errors;
      !this.$v.editedItem.disciplinary_action.required &&
        errors.push("Disciplinary Action is required.");
      return errors;
    },

    fileErrors() {
      
      const errors = [];
      if (!this.$v.file_input.$dirty) return errors;
      !this.$v.file_input.required &&
        errors.push("Attachment is required!");

      let file = this.file_input;
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

    transmitDateErrors() {
      const errors = [];

      if(this.dateErrors.transmit_date.msg)
      {
        errors.push(this.dateErrors.transmit_date.msg);
      }

      return errors;
    },

    returnDateErrors() {
      const errors = [];

      if(this.dateErrors.return_date.msg)
      {
        errors.push(this.dateErrors.return_date.msg);
      }

      return errors;
    },

    seriesErrors() {
      const errors = [];
      if (!this.$v.editedItem.series.$dirty) return errors;
      !this.$v.editedItem.series.required &&
        errors.push("Series of Disciplinary is required.");
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
      this.disciplinaries = this.data.disciplinaries;
    }
  },
};
</script>
