<template>
    <Dialog
        v-model:visible="experienceDialog"
        :style="{ width: '450px' }"
        header="editExperience"
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
            <small class="p-invalid" v-if="submitted && !experience.title">
                    titleIsRequired
                </small>
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
                        v-model="experience.is_current"
                    />
                    <label for="is_current1">Is Current</label>
                </div>
                <div class="field-radiobutton col-6">
                    <RadioButton
                        id="is_current2"
                        name="is_current2"
                        :value="false"
                        v-model="experience.is_current"
                    />
                    <label for="is_current2">Is Not Current</label>
                </div>
            </div>
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
            <Calendar showTime hourFormat="24"  required="true" id="start_date" v-model="experience.start_date" :class="[{ 'p-invalid': submitted && !experience.start_date },]" dateFormat="yy-mm-dd" />
            <small class="p-invalid" v-if="submitted && !experience.start_date">
                   start_dateIsRequired
                </small>
        </div>
        <div class="field">
            <label
                for="end_date"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >Start Date</label
            >
            <Calendar showTime hourFormat="24"  required="true" id="start_date" v-model="experience.end_date" :class="[{ 'p-invalid': submitted && !experience.end_date },]" dateFormat="yy-mm-dd" />
            <small class="p-invalid" v-if="submitted && !experience.end_date">
                end_dateIsRequired
            </small>
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
            <small class="p-invalid" v-if="submitted && !experience.type">
                typeIsRequired
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
                    @click="updateExperience"
                />
            </div>
        </template>
    </Dialog>
</template>

<script>
import {useToast} from "primevue/usetoast";

export default {
    props:["companies","users"],
    data() {
        return {
            experience: {},
            experienceDialog: false,
            submitted: false,
            selectedOption: null,
            userOption: null,
        };
    },
    methods: {
        uploadImage() {
            if (!this.$refs.fileUploader.files[0]) return;
            this.experience.image = this.$refs.fileUploader.files[0];
        }, //end of uploadImage
        updateExperience() {
            this.submitted = true;

            if ( this.experience.title.trim() && this.experience.description.trim() && this.experience.start_date && this.experience.end_date) {
                this.loading = true;
                const formData = new FormData();


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
                formData.append("title", this.experience.title);
                formData.append("description", this.experience.description);
                formData.append("start_date", convertedStartDateString ?? this.experience.start_date);
                formData.append("end_date", convertedEndDateString ?? this.experience.end_date);
                if(this.experience.is_current == true){
                    this.experience.is_current = 1;
                }else {
                    this.experience.is_current = 0;
                }
                formData.append('is_current',this.experience.is_current);
                formData.append('type',this.experience.type);
                formData.append('company_id',this.selectedOption.id);
                formData.append('user_id',this.userOption.id);

                formData.append("_method", "PUT");
                axios
                    .post("/api/admin/experiences/" + this.experience.id, formData)
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
        }, //end of updateExperience

        editExperience(editExperience) {
            this.experience = {...editExperience};
            this.experienceDialog = true;
        }, //end of editExperience

        openDialog(experience) {
            this.experience = experience;
            this.experienceDialog = true;
             this.selectedOption = this.experience.company;
             this.userOption = this.experience.user;
        }, //end of openDialog

        hideDialog() {
            this.experience = {};
            this.experienceDialog = false;
            this.submitted = false;
        }, //end of hideDialog
    }, //end of methods

    beforeMount() {
        this.toast = useToast();
    }, //end of beforeMount
};
</script>
