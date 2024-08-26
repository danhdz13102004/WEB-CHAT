<form
x-data="{name:@entangle('name')}"
@submit.prevent="$wire.changeName('Jane Doe')"
>
    @csrf
    <input type="text" x-model="name">
    <button type="submit">Change Name</button>
    <p>Current Name: {{ $name }}</p>
</form>
