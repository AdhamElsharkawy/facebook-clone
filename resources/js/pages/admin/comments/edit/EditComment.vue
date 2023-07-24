<template>
    <Dialog
        v-model:visible="commentDialog"
        :style="{ width: '450px' }"
        header="editComment"
        :modal="true"
        class="p-fluid"
    >
        <img
            :src="comment.image_path"
            :alt="comment.image"
            v-if="comment.image"
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
                v-model.trim="comment.name"
                required="true"
                autofocus
                type="text"
                :class="[
                    { 'p-invalid': submitted && !comment.name },
                    { 'text-right': $store.getters.isRtl },
                ]"
            />
            <small class="p-invalid" v-if="submitted && !comment.name">
                    nameIsRequired
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
                    @click="updateComment"
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
            comment: {},
            commentDialog: false,
            submitted: false,
        };
    },
    methods: {
        uploadImage() {
            if (!this.$refs.fileUploader.files[0]) return;
            this.comment.image = this.$refs.fileUploader.files[0];
        }, //end of uploadImage
        updateComment() {
            this.submitted = true;
            if ( this.comment.name.trim()) {
                this.loading = true;
                const formData = new FormData();

                formData.append("name", this.comment.name);
                if( typeof this.comment.image =='object'){
                    formData.append("image",this.comment.image)
                }

                formData.append("_method", "PUT");
                axios
                    .post("/api/admin/comments/" + this.comment.id, formData)
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
        }, //end of updateComment

        editComment(editComment) {
            this.comment = {...editComment};
            this.commentDialog = true;
        }, //end of editComment

        openDialog(comment) {
            this.comment = comment;
            this.commentDialog = true;
        }, //end of openDialog

        hideDialog() {
            this.comment = {};
            this.commentDialog = false;
            this.submitted = false;
        }, //end of hideDialog
    }, //end of methods

    beforeMount() {
        this.toast = useToast();
    }, //end of beforeMount
};
</script>
