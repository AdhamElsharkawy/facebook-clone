<template>
    <Loading v-if="loading" />
    <Dialog
        v-model:visible="newEducationDialog"
        :style="{ width: '450px' }"
        header="newEducation"
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
            <small class="p-invalid" v-if="submitted && !education.degree">{{
                $t("degreeIsRequired")
            }}</small>
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
            >End Date</label
            >
            <Calendar showTime hourFormat="24"  required="true" id="end_date" v-model="education.end_date" :class="[{ 'p-invalid': submitted && !education.end_date },]" dateFormat="yy-mm-dd" />
            <small class="p-invalid" v-if="submitted && !education.end_date">
                end_dateIsRequired
            </small>
        </div>


        <div class="field">
            <label
                for="users"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >User</label
            >
            <Dropdown v-model="selectedOptionTwo" :options="users" optionLabel="name"
                      placeholder="Select a User" class="w-full md:w-14rem"/>
        </div>

        <div class="field">
            <label
                for="colleges"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >College</label
            >
            <Dropdown v-model="selectedOption" :options="colleges" optionLabel="name"
                      placeholder="Select a College" class="w-full md:w-14rem"/>
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
                        name="is_current"
                        value=1
                        v-model="education.is_current"
                    />
                    <label for="is_current1">Is Current</label>
                </div>
                <div class="field-radiobutton col-6">
                    <RadioButton
                        id="is_current2"
                        name="is_current"
                        value=0
                        v-model="education.is_current"
                    />
                    <label for="is_current2">Is Not Current</label>
                </div>
            </div>
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
            <small class="p-invalid" v-if="submitted && !education.location">{{
                    $t("locationIsRequired")
                }}</small>
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
                    @click="createEducation"
                />
            </div>
        </template>
    </Dialog>
</template>
<script>
import { useToast } from "primevue/usetoast";

export default {
    props:["colleges","users"],
    emits: ["educationCreated"],
    data() {
        return {
            newEducationDialog: false,
            selectedOption: null,
            selectedOptionTwo: null,
            education: {
                degree: "",
                major: "",
                start_date: "",
                end_date: "",
                is_current: 0,
                location: "",
            },
            submitted: false,
            loading: false,
        };
    }, //end of data

    beforeMount() {
        this.toast = useToast();
    },

    methods: {
        createEducation() {
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

            this.submitted = true;
            if (
                this.education.degree.trim() &&
                this.education.major.trim() &&
                this.education.start_date &&
                this.education.end_date &&
                this.selectedOption &&
                this.selectedOptionTwo &&
                this.education.location.trim()

            ) {
                this.loading = true;
                const formData = new FormData();
                for (let key in this.education) {
                    formData.append(key, this.education[key]);
                }
                formData.append("college_id", this.selectedOption.id);
                formData.append("user_id", this.selectedOptionTwo.id);
                axios
                    .post("/api/admin/educations", formData, {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    })
                    .then((response) => {
                        this.toast.add({
                            severity: "success",
                            summary: "Successful",
                            detail: response.data.message,
                            life: 3000,
                        });
                        this.$emit("educationCreated");
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
        }, //end of createEducation

        openDialog() {
            this.newEducationDialog = true;
        }, //end of openDialog

        hideDialog() {
            this.education = {};
            this.submitted = false;
            this.newEducationDialog = false;
        }, //end of hideDialog
    },
};
</script>
