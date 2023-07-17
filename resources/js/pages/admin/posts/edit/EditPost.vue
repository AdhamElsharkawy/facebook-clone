<template>
    <Dialog
        v-model:visible="postDialog"
        :style="{ width: '450px' }"
        header="editPost"
        :modal="true"
        class="p-fluid"
    >

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
            </div>

            <small class="p-invalid" v-if="submitted && !comment">{{
                    threadIsRequired
                }}</small>
        </div>

        <div v-if="post.polls.length > 0">
            <div class="w-full mt-4 p-10">
                <button
                    type="button"
                    class="flex justify-start ml-2 rounded-md border px-3 py-2 bg-pink-600 text-white"
                    @click="addMore()"
                >
                    Add or Edit More Polls if needed
                </button>
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
                        <button
                            type="button"
                            class="ml-2 rounded-md border px-3 py-2 bg-red-600 text-white"
                            @click="remove(index)"
                            v-show="polls.length > 0"
                        >
                            Remove
                        </button>

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
            post: {},
            polls: [],
            postDialog: false,
            submitted: false,
            selectedOption: null,
        };
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
        updatePost() {
            console.log(this.polls);
            this.submitted = true;

            if (this.post.thread && this.post.thread.trim()) {
                this.loading = true;
                const formData = new FormData();
                formData.append("thread", this.post.thread);
                formData.append("_method", "PUT");
                axios
                    .post("/api/admin/posts/" + this.post.id, formData)
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
        }, //end of updatePost

        editPost(editPost) {
            this.post = {...editPost};
            this.postDialog = true;
        }, //end of editPost

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
