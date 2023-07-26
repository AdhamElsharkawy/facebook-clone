<template>
    <Loading v-if="loading" />
    <Dialog
        v-model:visible="newExperienceDialog"
        :style="{ width: '450px' }"
        header="newExperience"
        :modal="true"
        class="p-fluid"
    >


        <div class="field">
            <label
                for="title"
                :class="[{ 'float-right': $store.getters.isRtl }]"
                >title</label
            >
            <InputText
                id="title"
                v-model.trim="experience.title"
                required="true"
                autofocus
                type="text"
                :class="[
                    { 'p-invalid': submitted && !experience.title },
                    { 'text-right': $store.getters.isRtl },
                ]"
            />
            <small class="p-invalid" v-if="submitted && !experience.title">{{
                $t("titleIsRequired")
            }}</small>
        </div>

        <div class="field">
            <label
                for="description"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >description</label
            >
            <InputText
                id="description"
                v-model.trim="experience.description"
                required="true"
                autofocus
                type="text"
                :class="[
                    { 'p-invalid': submitted && !experience.description },
                    { 'text-right': $store.getters.isRtl },
                ]"
            />
            <small class="p-invalid" v-if="submitted && !experience.description">
                   descriptionIsRequired
                </small>
        </div>
        <div class="field">
            <label
                for="start_date"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >Start Date</label
            >
            <Calendar showTime hourFormat="24"  required="true" id="start_date" v-model="experience.start_date" :class="[{ 'p-invalid': submitted && !experience.start_date },]" dateFormat="yy-mm-dd" />
            <small class="p-invalid" v-if="submitted && !experience.start_date">
                start_dateIsRequired
            </small>
        </div>

        <div class="field">
            <label
                for="end_date"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >End Date</label
            >
            <Calendar showTime hourFormat="24"  required="true" id="end_date" v-model="experience.end_date" :class="[{ 'p-invalid': submitted && !experience.end_date },]" dateFormat="yy-mm-dd" />
            <small class="p-invalid" v-if="submitted && !experience.end_date">
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
                for="companies"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >Company</label
            >
            <Dropdown v-model="selectedOption" :options="companies" optionLabel="name"
                      placeholder="Select a Company" class="w-full md:w-14rem"/>
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
                        v-model="experience.is_current"
                    />
                    <label for="is_current1">Is Current</label>
                </div>
                <div class="field-radiobutton col-6">
                    <RadioButton
                        id="is_current2"
                        name="is_current"
                        value=0
                        v-model="experience.is_current"
                    />
                    <label for="is_current2">Is Not Current</label>
                </div>
            </div>
        </div>
        <div class="field">
            <label
                for="type"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >type</label
            >
            <InputText
                id="type"
                v-model.trim="experience.type"
                required="true"
                autofocus
                type="text"
                :class="[
                    { 'p-invalid': submitted && !experience.type },
                    { 'text-right': $store.getters.isRtl },
                ]"
            />
            <small class="p-invalid" v-if="submitted && !experience.type">{{
                    $t("typeIsRequired")
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
                    @click="createExperience"
                />
            </div>
        </template>
    </Dialog>
</template>
<script>
import { useToast } from "primevue/usetoast";

export default {
    props:["companies","users"],
    emits: ["experienceCreated"],
    data() {
        return {
            newExperienceDialog: false,
            selectedOption: null,
            selectedOptionTwo: null,
            experience: {
                title: "",
                description: "",
                start_date: "",
                end_date: "",
                is_current: 0,
                type: "",
            },
            submitted: false,
            loading: false,
        };
    }, //end of data

    beforeMount() {
        this.toast = useToast();
    },

    methods: {
        createExperience() {
            let regEx = '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/';
            let convertedStartDateString;
            if(this.experience.start_date != regEx && typeof this.experience.start_date == 'object'){
                const year = this.experience.start_date.getFullYear();
                const month = ('0' + (this.experience.start_date.getMonth() + 1)).slice(-2);
                const day = ('0' + this.experience.start_date.getDate()).slice(-2);
                const hours = ('0' + this.experience.start_date.getHours()).slice(-2);
                const minutes = ('0' + this.experience.start_date.getMinutes()).slice(-2);
                const seconds = ('0' + this.experience.start_date.getSeconds()).slice(-2);
                convertedStartDateString = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                this.experience.start_date = convertedStartDateString;
            }
            let convertedEndDateString;
            if(this.experience.end_date != regEx && typeof this.experience.end_date == 'object'){
                const year = this.experience.end_date.getFullYear();
                const month = ('0' + (this.experience.end_date.getMonth() + 1)).slice(-2);
                const day = ('0' + this.experience.end_date.getDate()).slice(-2);
                const hours = ('0' + this.experience.end_date.getHours()).slice(-2);
                const minutes = ('0' + this.experience.end_date.getMinutes()).slice(-2);
                const seconds = ('0' + this.experience.end_date.getSeconds()).slice(-2);
                convertedEndDateString = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                this.experience.end_date = convertedEndDateString;
            }

            this.submitted = true;
            if (
                this.experience.title.trim() &&
                this.experience.description.trim() &&
                this.experience.start_date &&
                this.experience.end_date &&
                this.selectedOption &&
                this.selectedOptionTwo &&
                this.experience.type.trim()

            ) {
                this.loading = true;
                const formData = new FormData();
                for (let key in this.experience) {
                    formData.append(key, this.experience[key]);
                }
                formData.append("company_id", this.selectedOption.id);
                formData.append("user_id", this.selectedOptionTwo.id);
                axios
                    .post("/api/admin/experiences", formData, {
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
                        this.$emit("experienceCreated");
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
        }, //end of createExperience

        openDialog() {
            this.newExperienceDialog = true;
        }, //end of openDialog

        hideDialog() {
            this.experience = {};
            this.submitted = false;
            this.newExperienceDialog = false;
        }, //end of hideDialog
    },
};
</script>
