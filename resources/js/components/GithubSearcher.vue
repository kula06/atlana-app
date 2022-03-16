<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header text-center h2" @click="closeUser">GitHub Searcher</div>

                    <div class="users-page" v-if="! user">
                        <div class="form p-2">
                            <input type="text" class="form-control" placeholder="Search for Users" @input="debounceUsersSearch">
                        </div>

                        <table class="table">
                            <tbody>
                            <tr v-for="user in users" @click="selectUser(user)" style="cursor: pointer;">
                                <td><img :src="user.avatar_url" alt="" width="64"></td>
                                <td>{{ user.login }}</td>
                                <td>Repo: {{ user.repositories_count }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="user-page" v-if="user">
                        <div class="container">
                            <div class="row mt-2">
                                <div class="col-6">
                                    <img :src="user.avatar_url" alt="" width="100%">
                                </div>
                                <div class="col-6">
                                    <ul style="list-style-type: none">
                                        <li>{{ user.login }}</li>
                                        <li>{{ user.email }}</li>
                                        <li>{{ user.location }}</li>
                                        <li>{{ user.created_at }}</li>
                                        <li>{{ user.followers }} Followers</li>
                                        <li>Following {{ user.following }}</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12">
                                    {{ user.bio }}
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="form">
                                    <input type="text" class="form-control" placeholder="Search for User's Repositories" @input="debounceUserSearchRepositories">
                                </div>
                            </div>
                        </div>
                        <div class="container">
                            <table class="table">
                                <tbody>
                                    <tr v-for="repo in user.repositories">
                                        <td>{{ repo.name }}</td>
                                        <td>
                                            <ul style="list-style-type: none">
                                                <li>{{ repo.forks_count }} Forks</li>
                                                <li>{{ repo.stargazers_count }} Stars</li>
                                            </ul>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import debounce from "@popperjs/core/lib/utils/debounce";
import _ from "lodash";

export default {
    data() {
        return {
            users: [],
            user: false
        };
    },
    mounted() {
        this.searchUsers();
    },
    methods: {
        searchUsers(query = '') {
            axios.get('/api/search', {
                params: { query }
            })
            .then(res => {
                this.users = res.data;
            });
        },
        debounceUsersSearch: _.debounce(function (e) {
            if (e.target.value !== '')
                this.searchUsers(e.target.value);
        }, 500),
        selectUser(user) {
            axios.get('/api/user/' + user.login)
                .then(res => this.user = res.data);
        },
        closeUser() {
            this.user = false;
        },
        searchUserRepositories(username, query = '') {
          axios.get(`/api/user/${username}/repositories`, {
              params: { query }
          })
          .then(res => this.user.repositories = res.data);
        },
        debounceUserSearchRepositories: _.debounce(function (e) {
            this.searchUserRepositories(this.user.login, e.target.value);
        }, 500),
    }
}
</script>
