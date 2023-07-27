<template>
    <Dialog
        v-model:visible="eventDialog"
        :style="{ width: '450px' }"
        header="editEvent"
        :modal="true"
        class="p-fluid"
    >
        <img
            :src="event.image_path"
            :alt="event.image"
            v-if="event.image"
            width="150"
            class="mt-0 mx-auto mb-5 block shadow-2"
        />
        <div class="field text-center">
            <div class="p-inputgroup">
                <div class="custom-file">
                    <FileUpload
                        mode="basic"
                        accept="image/*"
                        customUpload
                        :maxFileSize="2048000"
                        :chooseLabel="$t('chooseImage')"
                        @change="uploadImage"
                        ref="fileUploader"
                        class="m-0"
                    />
                </div>
            </div>
        </div>

        <div class="field">
            <label
                for="name"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >name</label
            >
            <InputText
                id="name"
                v-model.trim="event.name"
                required="true"
                autofocus
                type="text"
                :class="[
                    { 'p-invalid': submitted && !event.name },
                    { 'text-right': $store.getters.isRtl },
                ]"
            />
            <small class="p-invalid" v-if="submitted && !event.name">
                    nameIsRequired
                </small>
        </div>


        <div class="field">
            <label
                for="description"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >description</label
            >
            <InputText
                id="name"
                v-model.trim="event.description"
                required="true"
                autofocus
                type="text"
                :class="[
                    { 'p-invalid': submitted && !event.description },
                    { 'text-right': $store.getters.isRtl },
                ]"
            />
            <small class="p-invalid" v-if="submitted && !event.description">
                descriptionIsRequired
            </small>
        </div>



        <div class="field">
            <label
                for="start_date"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >Start Date</label
            >
            <Calendar showTime hourFormat="24"  required="true" id="start_date" v-model="event.start_date" :class="[{ 'p-invalid': submitted && !event.start_date },]" dateFormat="yy-mm-dd" />
            <small class="p-invalid" v-if="submitted && !event.start_date">
                   start_dateIsRequired
                </small>
        </div>
        <div class="field">
            <label
                for="end_date"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >Start Date</label
            >
            <Calendar showTime hourFormat="24"  required="true" id="start_date" v-model="event.end_date" :class="[{ 'p-invalid': submitted && !event.end_date },]" dateFormat="yy-mm-dd" />
            <small class="p-invalid" v-if="submitted && !event.end_date">
                end_dateIsRequired
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
                    @click="updateEvent"
                />
            </div>
        </template>
    </Dialog>
</template>

<script>
import {useToast} from "primevue/usetoast";

export default {
    data() {
        return {
            event: {},
            eventDialog: false,
            submitted: false,
        };
    },
    methods: {
        uploadImage() {
            if (!this.$refs.fileUploader.files[0]) return;
            this.event.image = this.$refs.fileUploader.files[0];
        }, //end of uploadImage
        updateEvent() {
            this.submitted = true;

            if ( this.event.name.trim() && this.event.description.trim() && this.event.start_date) {
                this.loading = true;
                const formData = new FormData();


                let regEx = '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/';
                let convertedStartDateString;
                if(this.event.start_date != regEx && typeof this.event.start_date == 'object'){
                    const year = this.event.start_date.getFullYear();
                    const month = ('0' + (this.event.start_date.getMonth() + 1)).slice(-2);
                    const day = ('0' + this.event.start_date.getDate()).slice(-2);
                    const hours = ('0' + this.event.start_date.getHours()).slice(-2);
                    const minutes = ('0' + this.event.start_date.getMinutes()).slice(-2);
                    const seconds = ('0' + this.event.start_date.getSeconds()).slice(-2);
                     convertedStartDateString = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                    this.event.start_date = convertedStartDateString;
                }
                let convertedEndDateString;
                if(this.event.end_date != regEx && typeof this.event.end_date == 'object'){
                    const year = this.event.end_date.getFullYear();
                    const month = ('0' + (this.event.end_date.getMonth() + 1)).slice(-2);
                    const day = ('0' + this.event.end_date.getDate()).slice(-2);
                    const hours = ('0' + this.event.end_date.getHours()).slice(-2);
                    const minutes = ('0' + this.event.end_date.getMinutes()).slice(-2);
                    const seconds = ('0' + this.event.end_date.getSeconds()).slice(-2);
                    convertedEndDateString = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                    this.event.end_date = convertedEndDateString;
                }
                formData.append("name", this.event.name);
                formData.append("description", this.event.description);
                formData.append("start_date", convertedStartDateString ?? this.event.start_date);
                formData.append("end_date", convertedEndDateString ?? this.event.end_date);
                if( typeof this.event.image =='object'){
                    formData.append("image",this.event.image)
                }

                formData.append("_method", "PUT");
                axios
                    .post("/api/admin/events/" + this.event.id, formData)
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
        }, //end of updateEvent

        editEvent(editEvent) {
            this.event = {...editEvent};
            this.eventDialog = true;
        }, //end of editEvent

        openDialog(event) {
            this.event = event;
            this.eventDialog = true;
            // this.selectedOption = this.event.department;
        }, //end of openDialog

        hideDialog() {
            this.event = {};
            this.eventDialog = false;
            this.submitted = false;
        }, //end of hideDialog
    }, //end of methods

    beforeMount() {
        this.toast = useToast();
    }, //end of beforeMount
};
</script>
