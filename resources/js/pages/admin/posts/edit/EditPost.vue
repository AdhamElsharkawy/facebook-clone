<template>
    <Dialog
        v-model:visible="postDialog"
        :style="{ width: '450px' }"
        header="editPost"
        :modal="true"
        class="p-fluid"
    >


        <div v-if="post.images" class="field text-center mb-4">
            <div class="p-inputgroup">
                <div class="custom-file ">
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
                required="true"
                autofocus
                type="text"
                :class="[
                    { 'p-invalid': submitted && !post.thread },
                    { 'text-right': $store.getters.isRtl },
                ]"
            />
            <small class="p-invalid" v-if="submitted && !post.thread">{{
                    threadIsRequired
                }}</small>
        </div>

        <div v-if="post.comments.length > 0" class="field">
            <label
                for="comments"
                :class="[{ 'float-right': $store.getters.isRtl }]"
            >All comments on the Post :</label
            >
            <div v-for="(comment, index) in post.comments" :key="index">
                <label
                    for="comment"
                    :class="[{ 'float-right': $store.getters.isRtl }]"
                >({{ comment.user.name }}) comment :</label>
                <InputText
                    id="comment"
                    v-model.trim="comment.thread"
                    required="true"
                    autofocus
                    type="text"
                    :class="[
                    { 'p-invalid': submitted && !comment },
                    { 'text-right': $store.getters.isRtl },
                ]"
                />
                <input type="hidden" name="_method" value="DELETE">
                <Button
                    :label="$t('delete comment')"
                    icon="pi pi-trash"
                    class="p-button-danger mt-2"
                    @click="deleteComment(comment.id)"
                    :disabled="
                                    !post.comments || !post.comments.length
                                "
                />
            </div>

            <small class="p-invalid" v-if="submitted && !comment">{{
                    threadIsRequired
                }}</small>
        </div>

        <div v-if="post.polls.length > 0">
            <div class="w-full mt-4 p-10">
                <Button
                    type="button"
                    class="p-button-success mt-2"
                    @click="addMore()"
                    label="Add More Polls"
                />
                <div v-if="post.pending">
                <div class="field mt-2">
                    <label
                        for="poll_end_date"
                        :class="[{ 'float-right': $store.getters.isRtl }]"
                    >Poll End Date</label
                    >
                    <Calendar showTime hourFormat="24" id="poll_end_date" v-model="post.poll_end_date" :class="[{ 'p-invalid': submitted && !post.poll_end_date },]" dateFormat="yy-mm-dd" />

                </div>
                    <small class="p-invalid" v-if="submitted && !post.poll_end_date">{{
                            threadIsRequired
                        }}</small>
                </div>

                <div v-for="(poll, index) in polls" :key="index">
                    <div class=" ml-2 mt-4">
                        <div class="col">
                            <label
                                for="poll.poll"
                                :class="[{ 'float-right': $store.getters.isRtl }]"
                            >poll name:</label>
                            <input
                                v-model="poll.poll"
                                placeholder="enter you poll name"
                                class="w-full pl-3 py-2 border border-indigo-500 rounded"
                                id="poll.poll"
                            />
                        </div>
                        <div class="col">
                            <label
                                for="poll.votes"
                                :class="[{ 'float-right': $store.getters.isRtl }]"
                            >poll value:</label>
                        <input
                            id="poll.votes"
                            type="number"
                            v-model.number="poll.votes"
                            placeholder="enter you poll votes"
                            class="w-full pl-3 py-2 border border-indigo-500 rounded"
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
import {useToast} from "primevue/usetoast";

export default {
    data() {
        return {
            images:[],
            post: {},
            polls: [],
            // comments: [
            //     ...this.post.comments,
            // ],
            postDialog: false,
            submitted: false,
            selectedOption: null,
        };
    },
    methods: {
        addMore() {
            // console.log(this.comments);
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
            this.post.images = this.$refs.fileUploader.files;
            console.log('comments', this.post.comments);


        },
        updatePost() {
            this.submitted = true;
            if (this.post.thread && this.post.thread.trim()) {
                this.loading = true;
                const formData = new FormData();
                let regEx = '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/';
                let convertedEndPollDateString;
                if(this.post.poll_end_date != regEx && typeof this.post.poll_end_date == 'object'){
                    const year = this.post.poll_end_date.getFullYear();
                    const month = ('0' + (this.post.poll_end_date.getMonth() + 1)).slice(-2);
                    const day = ('0' + this.post.poll_end_date.getDate()).slice(-2);
                    const hours = ('0' + this.post.poll_end_date.getHours()).slice(-2);
                    const minutes = ('0' + this.post.poll_end_date.getMinutes()).slice(-2);
                    const seconds = ('0' + this.post.poll_end_date.getSeconds()).slice(-2);
                    convertedEndPollDateString = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                    this.post.poll_end_date = convertedEndPollDateString;
                }
                formData.append("thread", this.post.thread);
                console.log('images',this.post.images);
                if( typeof this.post.images =='object' && this.post.images.length > 0){
                    for (let i = 0; i < this.post.images.length; i++) {
                        formData.append('images[]', this.post.images[i]);
                    }
                }
                if(this.post.polls.length > 0){
                    formData.append("polls", JSON.stringify(this.post.polls));
                }
                if(this.post.poll_end_date){
                    formData.append("poll_end_date", this.post.poll_end_date);
                }
                if (this.post.comments.length > 0) {
                    formData.append("comments", JSON.stringify(this.post.comments));
                }
                formData.append("_method", "PUT");
                axios
                    .post("/api/admin/posts/" + this.post.id, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }).then((response) => {
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
        }, //end of updatePost

        editPost(editPost) {
            this.post = {...editPost};
            this.postDialog = true;
        }, //end of editPost
        deleteComment(commentId) {
            this.loading = true;
            const formData = new FormData();
            formData.append("_method", "DELETE");
            axios
                    .post(`/api/admin/posts/delete/comment/${commentId}`,formData)
                .then((response) => {
                    this.toast.add({
                        severity: "success",
                        summary: "Successful",
                        detail: response.data.message,
                        life: 3000,
                    });
                    this.removeCommentFromList(commentId);
                    // this.hideDialog();
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
        }, //end of deleteComment
        removeCommentFromList(commentId) {
            this.post.comments = this.post.comments.filter(
                (comment) => comment.id !== commentId
            );
        }, //end of removeCommentFromList

        openDialog(post) {
            this.post = post;
            console.log(this.post);
            this.postDialog = true;
            this.polls = this.post.polls;
            console.log(this.polls);
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
