<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">

                        <div class="input-group mb-3">
                            <input name="username" type="search" v-model="username" v-on:keyup.enter="search" class="form-control" placeholder="Enter GitHub username" aria-label="Enter GitHub username" aria-describedby="basic-addon2" required autofocus>
                            <div class="input-group-append">
                                <button @click="search" class="btn btn-outline-secondary">Search</button>
                            </div>
                        </div>

                        <div class="spinner-border" role="status" v-if="loading">
                            <span class="sr-only"></span>
                        </div>
                        <h4 v-if="userNotFound">User with username "{{ username }}" not found</h4>

                        <div v-if="Object.keys(user).length">
                            <h4 v-if="user"><span class="text-secondary">Name:</span> {{ user.name }}</h4>
                            <h4 v-if="user"><span class="text-secondary">Number of followers:</span> {{ user.followers }}</h4>
                            <h4 class="mb-3"><span class="text-secondary">Followers:</span></h4>
                            <div class="row rbc">
                                <div class="col-sm-2" v-for="(avatar, index) in followers.avatar" :key="index" data-test="post">
                                    <img :src="avatar" class="img-fluid my-2 rounded" alt="">
                                </div>
                            </div>

                            <button @click="loadFollowers(followers.load_more_url)" v-if="followers.load_more_url" class="w-100 btn btn-primary btn-lg" type="submit">Load more</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
axios.defaults.headers.common['_token'] = window.csrfToken;

export default {
    data() {
        return {
            user: {},
            followers: {
                avatar: [],
                load_more_url: '',
            },
            username: '',
            loading: false,
            userNotFound: false,
        }
    },
    methods: {
        async search() {
            this.loading = true;
            this.userNotFound = false;
            this.user = {};
            this.followers = {
                avatar: [],
                load_more_url: '',
            };

            await axios.get('/api/search', {
                params: {
                    username: this.username
                }
            }).then((response) => {
                this.loading = false;
                this.user = response.data;
                this.loadFollowers(this.user.followers_url);
            }).catch(error => {
                this.userNotFound = true;
            }).finally(function () {
                this.loading = false;
            });
        },
        async loadFollowers(url) {
            await axios.post('/api/get_followers', {
                url: url
            }).then((response) => {
                this.followers.avatar.push(...response.data.avatar);
                this.followers.load_more_url = response.data.load_more_url;
            }).catch(error => {
                //console.log(error.toString())
            });
        }
    }
}
</script>
