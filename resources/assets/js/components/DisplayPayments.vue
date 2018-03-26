<template>
    <div>
        <form v-on:submit.prevent="getPayments">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>User:</label>
                        <select  class="form-control col-md-6" required v-model="form.account">
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
                        <input type="text" class="form-control col-md-6" v-mask="'9999-99-99'" v-model="form.from_date" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>To date:</label>
                        <input type="text" class="form-control col-md-6" v-mask="'9999-99-99'" v-model="form.to_date" />
                    </div>
                </div>
            </div>
            <br />
            <div class="form-group">
                <button class="btn btn-primary">Show payments</button>
            </div>
        </form>

        <div v-show="checkOperations()">
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
                    <td>Payer</td>
                    <td>Amount</td>
                    <td>Currency</td>
                    <td>Date</td>
                </tr>
                </thead>

                <tbody>
                <tr v-for="operation in operations">
                    <td>{{ operation.id }}</td>
                    <td>{{ operation.payee }}</td>
                    <td>{{ operation.payer }}</td>
                    <td>{{ operation.amount }}</td>
                    <td>{{ operation.currency }}</td>
                    <td>{{ operation.date }}</td>
                </tr>
                </tbody>

                <tfoot>
                <tr>
                    <td colspan="5">{{ system_currency }}: {{ system_sum }}</td>
                </tr>
                <tr>
                    <td colspan="5">{{ user_currency }}: {{ user_sum}}</td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                url: 'http://payment-system.d:8092/api/',
                form: {},
                users: {},
                operations: {},
                user_currency: '',
                system_currency: 'USD',
                system_sum: 0,
                user_sum: 0,
            }
        },

        created: function () {
            this.getUsers();
        },

        methods: {
            checkOperations() {
                return this.operations.length > 0;
            },
            getUsers() {
                this.axios({
                    method: 'get',
                    url: this.url + 'users',
                    params: this.form
                }).then(response => {
                    this.users = response.data.data;
                });
            },
            getPayments() {
                this.axios({
                    method: 'get',
                    url: this.url + 'payments/operations',
                    params: this.form
                }).then(response => {
                    this.operations = response.data.data;
                    this.user_currency = response.data.meta.user.currency;
                    this.system_sum = response.data.meta.sums.default_sum;
                    this.user_sum = response.data.meta.sums.native_sum;
                });
            },
            getCsvPayments() {
                this.axios({
                    method: 'get',
                    url: this.url + 'payments/operations/download',
                    responseType: 'arraybuffer',
                    params: this.form
                })
                .then(function (response) {
                    let blob = new Blob([response.data], {type: 'text/csv'});
                    let link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'payments.csv';
                    link.click()
                });
            }
        }
    }
</script>