<template>
    <div>
        <form v-on:submit.prevent="findPayments">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>User:</label>
                        <input type="text" class="form-control col-md-6" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>From date:</label>
                        <input type="text" class="form-control col-md-6" v-mask="'9999-99-99'" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>To date:</label>
                        <input type="text" class="form-control col-md-6" v-mask="'9999-99-99'" />
                    </div>
                </div>
            </div>
            <br />
            <div class="form-group">
                <button class="btn btn-primary">Show payments</button>
            </div>
        </form>
    </div>

    <!--<div>-->
        <!--<b-alert :show="checkToken()" variant="danger">Token is required for editing from!</b-alert>-->

        <!--<h1>Apartments</h1>-->

        <!--<div class="row">-->
            <!--<div class="col-md-10"></div>-->
            <!--<div class="col-md-2">-->
                <!--<router-link :to="{ name: 'CreateApartment' }" class="btn btn-primary">Create Apartment</router-link>-->
            <!--</div>-->
        <!--</div>-->

        <!--<br />-->

        <!--<table class="table table-hover">-->
            <!--<thead>-->
            <!--<tr>-->
                <!--<td>ID</td>-->
                <!--<td>Move in date</td>-->
                <!--<td>Country</td>-->
                <!--<td>Post code/City/Street</td>-->
                <!--<td>Email</td>-->
                <!--<td>Created at</td>-->
            <!--</tr>-->
            <!--</thead>-->

            <!--<tbody>-->
            <!--<tr v-for="item in items">-->
                <!--<td>{{ item.id }}</td>-->
                <!--<td>{{ item.move_in_date }}</td>-->
                <!--<td>{{ item.country }}</td>-->
                <!--<td>{{ item.post_code }}/{{ item.city }}/{{ item.street }}</td>-->
                <!--<td>{{ item.email }}</td>-->
                <!--<td>{{ item.created_at }}</td>-->
            <!--</tr>-->
            <!--</tbody>-->
        <!--</table>-->
    <!--</div>-->
</template>

<script>

    import {app_url} from "../app";

    export default {
        // data() {
        //     return {
        //         item: {},
        //         token: this.$route.query.token
        //     }
        // },
        //
        // created: function()
        // {
        //     this.fetchApartments();
        // },
        //
        // methods: {
        //     fetchApartments()
        //     {
        //         let endpoint = app_url + 'apartments';
        //
        //         this.axios.get(endpoint)
        //             .then((response) => {
        //                 this.items = response.data;
        //             });
        //     },
        //     checkToken() {
        //         return typeof this.token === 'undefined' || !this.token;
        //     },
        //     getApartment()
        //     {
        //         let endpoint = app_url + 'apartments/' + this.$route.params.id;
        //
        //         this.axios.get(endpoint)
        //             .then((response) => {
        //                 this.item = response.data;
        //             });
        //     },
        //     updateApartment()
        //     {
        //         let endpoint = app_url + 'apartments/' + this.$route.params.id;
        //
        //         if (!this.checkToken()) {
        //             endpoint += '?token=' + this.token;
        //         }
        //
        //         this.axios.post(endpoint, this.item)
        //             .then(response => {
        //                 this.$router.push({
        //                     name: 'DisplayApartment'
        //                 });
        //                 console.log(response)
        //             })
        //             .catch(error => {
        //                 if (error.response.status === 403)
        //                 {
        //                     this.$refs.tokenModalRef.show();
        //                 }
        //                 console.log(error.response)
        //             });
        //     },
        //     findPayments()
        //     {
        //
        //     }
        // }
    }
</script>