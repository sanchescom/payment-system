<template>
    <div>
        <form v-on:submit.prevent="getPayments">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>User:</label>
                        <select  class="form-control col-md-6" v-model="item.account">
                            <option v-for="user in users" v-bind:value="user.account">
                                {{ user.name }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>From date:</label>
                        <input type="text" class="form-control col-md-6" v-mask="'9999-99-99'" v-model="item.date_from" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>To date:</label>
                        <input type="text" class="form-control col-md-6" v-mask="'9999-99-99'" v-model="item.date_to" />
                    </div>
                </div>
            </div>
            <br />
            <div class="form-group">
                <button class="btn btn-primary">Show payments</button>
            </div>
        </form>
    </div>

    <div v-if="checkOperations()">
        <h1>Payments</h1>

        <div class="row">
            <div class="col-md-10"></div>
            <div class="col-md-2">
                <button class="btn btn-primary" v-on:click="getCsvPayments">Download in CSV</button>
            </div>
        </div>

        <br />

        <table class="table table-hover">
            <thead>
            <tr>
                <td>ID</td>
                <td>Payee</td>
                <td>Amount</td>
                <td>Currency</td>
            </tr>
            </thead>

            <tbody>
            <tr v-for="item in items">
                <td>{{ item.id }}</td>
                <td>{{ item.payee }}</td>
                <td>{{ item.amount }}</td>
                <td>{{ item.currency }}</td>
            </tr>
            </tbody>
        </table>
    </div>
    <div v-else>
        <b-alert variant="warning">Please chose user from list</b-alert>
    </div>
</template>

<script>

    import {app_url} from "../app";

    export default {
         data() {
             return {
                 item: {},
                 users: {},
                 operations: {}
             }
         },

         created: function()
         {
             let endpoint = app_url + 'users';

             this.axios.get(endpoint)
                 .then((response) => {
                     this.users = response.data;
                 });
         },

         methods: {
             checkOperations()
             {
                 return this.operations.length > 0;
             },
             getPayments()
             {
                 let endpoint = app_url + 'payments/operations';

                 this.axios.get(endpoint, this.item)
                     .then(response => {
                         console.log(this.item)
                     })
                     .catch(error => {
                         console.log(error.response)
                     });
             },
             getCsvPayments()
             {
                 let endpoint = app_url + 'payments/download';

                 this.axios({
                     method:'get',
                     url:endpoint,
                     responseType:'arraybuffer',
                     data: this.item
                 })
                     .then(function(response) {
                         let blob = new Blob([response.data], { type:   'text/csv' } )
                         let link = document.createElement('a')
                         link.href = window.URL.createObjectURL(blob)
                         link.download = 'payments.csv'
                         link.click()

                     });
             }
         }
    }
</script>