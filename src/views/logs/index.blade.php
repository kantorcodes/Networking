@extends('networking::layout.main')

@section('content')

<table class="table table-responsive table-bordered text-center">
    <thead>
      <tr>
         <th>Endpoint Url</th>
         <th>Response Status Code</th>
         <th>Response Body</th>
         <th>Response Cookies</th>
         <th>Response Headers</th>
         </tr>
         </thead>
         <tbody>

         @if(!empty($requests))

             @foreach($requests->getCollection()->all() as $request)
                <tr>
                <td>{{$request->url}}</td>
                <td>{{$request->status}}</td>
                <td>{{$request->body}}</td>
                <td>{{$request->cookies}}</td>
                <td>{{$request->headers}}</td>
                </tr>
             @endforeach
             {{ $requests->links()}}
         @endif

       </tbody>
</table>
@stop