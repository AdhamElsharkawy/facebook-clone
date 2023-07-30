<template>
    <Dialog
        v-model:visible="postDialog"
        :style="{ width: '450px' }"
        header="editPost"
        :modal="true"
        class="p-fluid"
    >
        <div class="field text-center mb-4">
            <div class="p-inputgroup">
                <div class="custom-file">
                    <FileUpload
                        mode="basic"
                        accept="image/*"
                        customUpload
                        :multiple="true"
                        :maxFileSize="2048000"
                        :chooseLabel="$t('chooseImage')"
                        @change="handleFileChange"
                        ref="fileUploader"
                        class="m-0"
                    />
                </div>
            </div>
        </div>

        <div class="field">
            <label
                for="thread"
                :class="[{ 'float-right': $store.getters.isRtl }]"
                >thread</label
            >
            <InputText
                id="thread"
                v-model.trim="post.thread"
                autofocus
                type="text"
                :class="[
                    { 'p-invalid': submitted && !post.thread },
                    { 'text-right': $store.getters.isRtl },
                ]"
            />
            <!-- <small class="p-invalid" v-if="submitted && !post.thread">{{
                threadIsRequired
            }}</small> -->
        </div>

        <div v-if="post.pending">
            <Checkbox v-model="post.postNow" inputId="create" :binary="true" />
            <label for="create" class="ml-2"> Post Now </label>
            <div class="field mt-2">
                <label
                    for="created_at"
                    :class="[{ 'float-right': $store.getters.isRtl }]"
                    >Created At</label
                >
                <Calendar
                    showTime
                    hourFormat="24"
                    id="created_at"
                    v-model="post.created_at"
                    :class="[
                        {
                            'p-invalid': submitted && !post.created_at,
                        },
                    ]"
                    dateFormat="yy-mm-dd"
                />
            </div>
            <small class="p-invalid" v-if="submitted && !post.created_at">{{
                CreatedAtIsRequired
            }}</small>
        </div>

        <div v-if="post.polls.length > 0">
            <div>
                <div class="field mt-2">
                    <label
                        for="poll_end_date"
                        :class="[{ 'float-right': $store.getters.isRtl }]"
                        >Poll End Date</label
                    >
                    <Calendar
                        showTime
                        hourFormat="24"
                        id="poll_end_date"
                        v-model="post.poll_end_date"
                        :class="[
                            {
                                'p-invalid': submitted && !post.poll_end_date,
                            },
                        ]"
                        dateFormat="yy-mm-dd"
                    />
                </div>
                <small
                    class="p-invalid"
                    v-if="submitted && !post.poll_end_date"
                    >{{ threadIsRequired }}</small
                >
            </div>
            <div>
                <div class="field mt-2">
                    <label
                        for="poll_caption"
                        :class="[{ 'float-right': $store.getters.isRtl }]"
                        >Poll Caption</label
                    >
                    <InputText
                        id="poll_caption"
                        v-model.trim="post.poll_caption"
                        required="true"
                        autofocus
                        type="text"
                        :class="[
                            { 'p-invalid': submitted && !post.poll_caption },
                            { 'text-right': $store.getters.isRtl },
                        ]"
                    />
                </div>
                <small
                    class="p-invalid"
                    v-if="submitted && !post.poll_end_date"
                    >{{ threadIsRequired }}</small
                >
            </div>
            <div class="w-full mt-4 p-10">
                <Button
                    type="button"
                    class="p-button-success mt-2"
                    @click="addMore()"
                    label="Add More Polls"
                />
                <div v-for="(poll, index) in polls" :key="index">
                    <div class="ml-2 mt-4">
                        <div class="col">
                            <label
                                for="poll.poll"
                                :class="[
                                    { 'float-right': $store.getters.isRtl },
                                ]"
                                >poll name:</label
                            >
                            <input
                                v-model="poll.poll"
                                type="text"
                                required="true"
                                placeholder="enter you poll name"
                                class="w-full pl-3 py-2 border border-indigo-500 rounded"
                                id="poll.poll"
                            />
                        </div>
                        <Button
                            type="button"
                            class="p-button-danger mt-2"
                            @click="remove(index)"
                            v-show="polls.length > 0"
                            label="Remove"
                        />
                    </div>
                </div>
            </div>
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
                    @click="updatePost"
                />
            </div>
        </template>
    </Dialog>
</template>

<script>
import { useToast } from "primevue/usetoast";

export default {
    data() {
        return {
            images: [],
            post: {
                postNow: false,
            },
            polls: [],
            postDialog: false,
            submitted: false,
            selectedOption: null,
        };
    },
    watch: {
        post: {
            handler() {
                // console.log("post", this.post);
            },
            deep: true,
        },
    },
    methods: {
        addMore() {
            this.polls.push({
                poll: "",
                post_id: this.post.id,
            });
        },
        remove(index) {
            this.polls.splice(index, 1);
        },
        handleFileChange() {
            if (!this.$refs.fileUploader.files.length) return;
            this.images = this.$refs.fileUploader.files;
        },
        updatePost() {
            this.submitted = true;
            this.loading = true;
            const formData = new FormData();
            let regEx = "/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/";
            let convertedEndPollDateString;
            if (
                this.post.poll_end_date != regEx &&
                typeof this.post.poll_end_date == "object"
            ) {
                const year = this.post.poll_end_date.getFullYear();
                const month = (
                    "0" +
                    (this.post.poll_end_date.getMonth() + 1)
                ).slice(-2);
                const day = ("0" + this.post.poll_end_date.getDate()).slice(-2);
                const hours = ("0" + this.post.poll_end_date.getHours()).slice(
                    -2
                );
                const minutes = (
                    "0" + this.post.poll_end_date.getMinutes()
                ).slice(-2);
                const seconds = (
                    "0" + this.post.poll_end_date.getSeconds()
                ).slice(-2);
                convertedEndPollDateString = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                this.post.poll_end_date = convertedEndPollDateString;
            }
            let convertedCreatedDateString;
            if (
                this.post.created_at != regEx &&
                typeof this.post.created_at == "object"
            ) {
                const year = this.post.created_at.getFullYear();
                const month = (
                    "0" +
                    (this.post.created_at.getMonth() + 1)
                ).slice(-2);
                const day = ("0" + this.post.created_at.getDate()).slice(-2);
                const hours = ("0" + this.post.created_at.getHours()).slice(-2);
                const minutes = ("0" + this.post.created_at.getMinutes()).slice(
                    -2
                );
                const seconds = ("0" + this.post.created_at.getSeconds()).slice(
                    -2
                );
                convertedCreatedDateString = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                this.post.created_at = convertedCreatedDateString;
            }
            formData.append("thread", this.post.thread ? this.post.thread : "");
            if (typeof this.images == "object" && this.images.length > 0) {
                for (let i = 0; i < this.images.length; i++) {
                    formData.append("images[]", this.images[i]);
                }
            }
            if (this.post.polls.length > 0) {
                formData.append("polls", JSON.stringify(this.post.polls));
            }
            if (this.post.poll_end_date) {
                formData.append("poll_end_date", this.post.poll_end_date);
            }

            formData.append(
                "poll_caption",
                this.post.poll_caption ? this.post.poll_caption : ""
            );

            if (this.post.pending) {
                formData.append("created_at", this.post.created_at);
            }
            formData.append("postNow", this.post.postNow ? 1 : 0);
            // if (this.post.comments.length > 0) {
            //     formData.append(
            //         "comments",
            //         JSON.stringify(this.post.comments)
            //     );
            // }
            formData.append("_method", "PUT");
            axios
                .post("/api/admin/posts/" + this.post.id, formData, {
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
        }, //end of updatePost
        editPost(editPost) {
            this.post = { ...editPost };
            this.postDialog = true;
        }, //end of editPost
        openDialog(post) {
            this.post = post;
            this.postDialog = true;
            this.polls = this.post.polls;
        }, //end of openDialog
        hideDialog() {
            this.post = {};
            this.postDialog = false;
            this.submitted = false;
        }, //end of hideDialog
    }, //end of methods
    beforeMount() {
        this.toast = useToast();
    }, //end of beforeMount
};
</script>
