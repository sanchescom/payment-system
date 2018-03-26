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

    <div>

    </div>
</template>

<script>


    export default {


    }
</script>