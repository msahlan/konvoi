@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Welcome</div>

                <div class="panel-body" id="app">
                    <h2>@{{ hello }}</h2>
                    <input type="text" name="" v-model="hello" />
                    <button v-on:click="reverseMessage">Reverse Message</button>

                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    new Vue({
        el: '#app',
        data:{
            hello: 'Hello Dolly !!'
        },
        methods:{
            reverseMessage: function(){
                this.hello = this.hello.split('').reverse().join('');
            }
        }
    });    


</script>

@endsection
