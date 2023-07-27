<template>
    <Dialog
        v-model:visible="educationDialog"
        :style="{ width: '450px' }"
        header="editEducation"
        :modal="true"
        class="p-fluid"
    >


        <div class="field">
            <label
                for="degree"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >degree</label
            >
            <InputText
                id="degree"
                v-model.trim="education.degree"
                required="true"
                autofocus
                type="text"
                :class="[
                    { 'p-invalid': submitted && !education.degree },
                    { 'text-right': $store.getters.isRtl },
                ]"
            />
            <small class="p-invalid" v-if="submitted && !education.degree">
                    degreeIsRequired
                </small>
        </div>

        <div class="field">
            <label
                class="mb-3"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            > Is Current ?</label
            >
            <div class="formgrid grid">
                <div class="field-radiobutton col-6">
                    <RadioButton
                        id="is_current1"
                        name="is_current1"
                        :value="true"
                        v-model="education.is_current"
                    />
                    <label for="is_current1">Is Current</label>
                </div>
                <div class="field-radiobutton col-6">
                    <RadioButton
                        id="is_current2"
                        name="is_current2"
                        :value="false"
                        v-model="education.is_current"
                    />
                    <label for="is_current2">Not</label>
                </div>
            </div>
        </div>


        <div class="field">
            <label
                for="major"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >major</label
            >
            <InputText
                id="major"
                v-model.trim="education.major"
                required="true"
                autofocus
                type="text"
                :class="[
                    { 'p-invalid': submitted && !education.major },
                    { 'text-right': $store.getters.isRtl },
                ]"
            />
            <small class="p-invalid" v-if="submitted && !education.major">
                majorIsRequired
            </small>
        </div>

        <div class="field">
            <label
                for="colleges"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >Colleges</label
            >
            <Dropdown v-model="selectedOption" :options="colleges" optionLabel="name"
                      placeholder="Select a College" class="w-full md:w-14rem"/>
        </div>

        <div class="field">
            <label
                for="users"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >Users</label
            >
            <Dropdown v-model="userOption" :options="users" optionLabel="name"
                      placeholder="Select a College" class="w-full md:w-14rem"/>
        </div>



        <div class="field">
            <label
                for="start_date"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >Start Date</label
            >
            <Calendar showTime hourFormat="24"  required="true" id="start_date" v-model="education.start_date" :class="[{ 'p-invalid': submitted && !education.start_date },]" dateFormat="yy-mm-dd" />
            <small class="p-invalid" v-if="submitted && !education.start_date">
                   start_dateIsRequired
                </small>
        </div>
        <div class="field">
            <label
                for="end_date"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >Start Date</label
            >
            <Calendar showTime hourFormat="24"  required="true" id="start_date" v-model="education.end_date" :class="[{ 'p-invalid': submitted && !education.end_date },]" dateFormat="yy-mm-dd" />
            <small class="p-invalid" v-if="submitted && !education.end_date">
                end_dateIsRequired
            </small>
        </div>

        <div class="field">
            <label
                for="location"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >location</label
            >
            <InputText
                id="location"
                v-model.trim="education.location"
                required="true"
                autofocus
                type="text"
                :class="[
                    { 'p-invalid': submitted && !education.location },
                    { 'text-right': $store.getters.isRtl },
                ]"
            />
            <small class="p-invalid" v-if="submitted && !education.location">
                locationIsRequired
            </small>
        </div>



        <template #footer>
            <div
                :class="{
                    'flex flex-row-reverse float-left': $store.getters.isRtl,
                }"
            >
                <Button
                    :label="$t('cancel')"
                    icon="pi pi-times"
                    class="p-button-text"
                    @click="hideDialog"
                />
                <Button
                    :label="$t('submit')"
                    icon="pi pi-check"
                    class="p-button-text"
                    @click="updateEducation"
                />
            </div>
        </template>
    </Dialog>
</template>

<script>
import {useToast} from "primevue/usetoast";

export default {
    props:["colleges","users"],
    data() {
        return {
            education: {},
            educationDialog: false,
            submitted: false,
            selectedOption: null,
            userOption: null,
        };
    },
    methods: {
        uploadImage() {
            if (!this.$refs.fileUploader.files[0]) return;
            this.education.image = this.$refs.fileUploader.files[0];
        }, //end of uploadImage
        updateEducation() {
            this.submitted = true;

            if ( this.education.degree.trim() && this.education.major.trim() && this.education.start_date && this.education.end_date) {
                this.loading = true;
                const formData = new FormData();


                let regEx = '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/';
                let convertedStartDateString;
                if(this.education.start_date != regEx && typeof this.education.start_date == 'object'){
                    const year = this.education.start_date.getFullYear();
                    const month = ('0' + (this.education.start_date.getMonth() + 1)).slice(-2);
                    const day = ('0' + this.education.start_date.getDate()).slice(-2);
                    const hours = ('0' + this.education.start_date.getHours()).slice(-2);
                    const minutes = ('0' + this.education.start_date.getMinutes()).slice(-2);
                    const seconds = ('0' + this.education.start_date.getSeconds()).slice(-2);
                     convertedStartDateString = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                    this.education.start_date = convertedStartDateString;
                }
                let convertedEndDateString;
                if(this.education.end_date != regEx && typeof this.education.end_date == 'object'){
                    const year = this.education.end_date.getFullYear();
                    const month = ('0' + (this.education.end_date.getMonth() + 1)).slice(-2);
                    const day = ('0' + this.education.end_date.getDate()).slice(-2);
                    const hours = ('0' + this.education.end_date.getHours()).slice(-2);
                    const minutes = ('0' + this.education.end_date.getMinutes()).slice(-2);
                    const seconds = ('0' + this.education.end_date.getSeconds()).slice(-2);
                    convertedEndDateString = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                    this.education.end_date = convertedEndDateString;
                }
                formData.append("degree", this.education.degree);
                formData.append("major", this.education.major);
                formData.append("start_date", convertedStartDateString ?? this.education.start_date);
                formData.append("end_date", convertedEndDateString ?? this.education.end_date);
                if(this.education.is_current == true){
                    this.education.is_current = 1;
                }else {
                    this.education.is_current = 0;
                }
                formData.append('is_current',this.education.is_current);
                formData.append('location',this.education.location);
                formData.append('college_id',this.selectedOption.id);
                formData.append('user_id',this.userOption.id);

                formData.append("_method", "PUT");
                axios
                    .post("/api/admin/educations/" + this.education.id, formData)
                    .then((response) => {
                        this.toast.add({
                            severity: "success",
                            summary: "Successful",
                            detail: response.data.message,
                            life: 3000,
                        });
                        this.hideDialog();
                    })
                    .catch((errors) => {
                        if (errors.response) {
                            this.toast.add({
                                severity: "error",
                                summary: "Error",
                                detail: errors.response.data.message,
                                life: 15000,
                            });
                        }
                    })
                    .then(() => {
                        this.loading = false;
                    });
            }
        }, //end of updateEducation

        editEducation(editEducation) {
            this.education = {...editEducation};
            this.educationDialog = true;
        }, //end of editEducation

        openDialog(education) {
            this.education = education;
            this.educationDialog = true;
             this.selectedOption = this.education.college;
             this.userOption = this.education.user;
        }, //end of openDialog

        hideDialog() {
            this.education = {};
            this.educationDialog = false;
            this.submitted = false;
        }, //end of hideDialog
    }, //end of methods

    beforeMount() {
        this.toast = useToast();
    }, //end of beforeMount
};
</script>
