<template>
    <Dialog
        v-model:visible="certificationDialog"
        :style="{ width: '450px' }"
        header="editCertification"
        :modal="true"
        class="p-fluid"
    >


        <div class="field">
            <label
                for="major"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >major</label
            >
            <InputText
                id="major"
                v-model.trim="certification.major"
                required="true"
                autofocus
                type="text"
                :class="[
                    { 'p-invalid': submitted && !certification.major },
                    { 'text-right': $store.getters.isRtl },
                ]"
            />
            <small class="p-invalid" v-if="submitted && !certification.major">
                majorIsRequired
            </small>
        </div>



        <div class="field">
            <label
                class="mb-3"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            > Is Current</label
            >
            <div class="formgrid grid">
                <div class="field-radiobutton col-6">
                    <RadioButton
                        id="is_current1"
                        name="is_current1"
                        :value="true"
                        v-model="certification.is_current"
                    />
                    <label for="is_current1">Is Current</label>
                </div>
                <div class="field-radiobutton col-6">
                    <RadioButton
                        id="is_current2"
                        name="is_current2"
                        :value="false"
                        v-model="certification.is_current"
                    />
                    <label for="is_current2">Is Not Current</label>
                </div>
            </div>
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
                      placeholder="Select a User" class="w-full md:w-14rem"/>
        </div>



        <div class="field">
            <label
                for="start_date"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >Start Date</label
            >
            <Calendar showTime hourFormat="24"  required="true" id="start_date" v-model="certification.start_date" :class="[{ 'p-invalid': submitted && !certification.start_date },]" dateFormat="yy-mm-dd" />
            <small class="p-invalid" v-if="submitted && !certification.start_date">
                   start_dateIsRequired
                </small>
        </div>
        <div class="field">
            <label
                for="end_date"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >End Date</label
            >
            <Calendar showTime hourFormat="24" id="start_date" v-model="certification.end_date" :class="[{ 'p-invalid': submitted && !certification.end_date },]" dateFormat="yy-mm-dd" />
            <small class="p-invalid" v-if="submitted && !certification.end_date">
                end_dateIsRequired
            </small>
        </div>

        <div class="field">
            <label
                for="valid_until"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >Valid Until</label
            >
            <Calendar showTime hourFormat="24"  id="valid_until" v-model="certification.valid_until" :class="[{ 'p-invalid': submitted && !certification.valid_until },]" dateFormat="yy-mm-dd" />
            <small class="p-invalid" v-if="submitted && !certification.valid_until">
                valid_untilIsRequired
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
                v-model.trim="certification.location"
                autofocus
                type="text"
                :class="[
                    { 'p-invalid': submitted && !certification.location },
                    { 'text-right': $store.getters.isRtl },
                ]"
            />
            <small class="p-invalid" v-if="submitted && !certification.location">
                locationIsRequired
            </small>
        </div>

        <div class="field">
            <label
                for="confirmation_link"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >confirmation_link</label
            >
            <InputText
                id="confirmation_link"
                v-model.trim="certification.confirmation_link"
                autofocus
                type="text"
                :class="[
                    { 'p-invalid': submitted && !certification.confirmation_link },
                    { 'text-right': $store.getters.isRtl },
                ]"
            />
            <small class="p-invalid" v-if="submitted && !certification.confirmation_link">
                confirmation_linkIsRequired
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
                    @click="updateCertification"
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
            certification: [{
                // college: null,
                // college_id: null,
            }],
            certificationDialog: false,
            submitted: false,
            selectedOption: null,
            userOption: null,
        };
    },
    methods: {
        uploadImage() {
            if (!this.$refs.fileUploader.files[0]) return;
            this.certification.image = this.$refs.fileUploader.files[0];
        }, //end of uploadImage
        updateCertification() {
            this.submitted = true;

            if ( this.certification.major.trim() && this.certification.start_date && this.certification.end_date) {
                this.loading = true;
                const formData = new FormData();


                let regEx = '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/';
                let convertedStartDateString;
                if(this.certification.start_date != regEx && typeof this.certification.start_date == 'object'){
                    const year = this.certification.start_date.getFullYear();
                    const month = ('0' + (this.certification.start_date.getMonth() + 1)).slice(-2);
                    const day = ('0' + this.certification.start_date.getDate()).slice(-2);
                    const hours = ('0' + this.certification.start_date.getHours()).slice(-2);
                    const minutes = ('0' + this.certification.start_date.getMinutes()).slice(-2);
                    const seconds = ('0' + this.certification.start_date.getSeconds()).slice(-2);
                     convertedStartDateString = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                    this.certification.start_date = convertedStartDateString;
                }
                let convertedEndDateString;
                if(this.certification.end_date != regEx && typeof this.certification.end_date == 'object'){
                    const year = this.certification.end_date.getFullYear();
                    const month = ('0' + (this.certification.end_date.getMonth() + 1)).slice(-2);
                    const day = ('0' + this.certification.end_date.getDate()).slice(-2);
                    const hours = ('0' + this.certification.end_date.getHours()).slice(-2);
                    const minutes = ('0' + this.certification.end_date.getMinutes()).slice(-2);
                    const seconds = ('0' + this.certification.end_date.getSeconds()).slice(-2);
                    convertedEndDateString = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                    this.certification.end_date = convertedEndDateString;
                }
                let convertedValidUntilString;
                if(this.certification.valid_until != regEx && typeof this.certification.valid_until == 'object' && this.certification.valid_until){
                    const year = this.certification.valid_until.getFullYear();
                    const month = ('0' + (this.certification.valid_until.getMonth() + 1)).slice(-2);
                    const day = ('0' + this.certification.valid_until.getDate()).slice(-2);
                    const hours = ('0' + this.certification.valid_until.getHours()).slice(-2);
                    const minutes = ('0' + this.certification.valid_until.getMinutes()).slice(-2);
                    const seconds = ('0' + this.certification.valid_until.getSeconds()).slice(-2);
                    convertedValidUntilString = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                    this.certification.valid_until = convertedValidUntilString;
                }
                formData.append("major", this.certification.major);
                formData.append("start_date", convertedStartDateString ?? this.certification.start_date);
                formData.append("end_date", convertedEndDateString ?? this.certification.end_date);
                formData.append("valid_until", convertedValidUntilString ?? this.certification.valid_until);
                if(this.certification.is_current == true){
                    this.certification.is_current = 1;
                }else {
                    this.certification.is_current = 0;
                }
                formData.append('is_current',this.certification.is_current);
                formData.append('location',this.certification.location);
                formData.append('confirmation_link',this.certification.confirmation_link);
                formData.append('college_id',this.selectedOption.id);
                formData.append('user_id',this.userOption.id);

                formData.append("_method", "PUT");
                axios
                    .post("/api/admin/certifications/" + this.certification.id, formData)
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
        }, //end of updateCertification

        editCertification(editCertification) {
            this.certification = {...editCertification};
            this.certificationDialog = true;
        }, //end of editCertification

        openDialog(certification) {
            this.certification = certification;
            this.certificationDialog = true;
             this.selectedOption = this.certification.college;
             if( this.selectedOption == null){
                 this.selectedOption = {"id": null, "name": "No College"};
             }
             this.userOption = this.certification.user;

        }, //end of openDialog

        hideDialog() {
            this.certification = {};
            this.certificationDialog = false;
            this.submitted = false;
        }, //end of hideDialog
    }, //end of methods

    beforeMount() {
        this.toast = useToast();
    }, //end of beforeMount
};
</script>
