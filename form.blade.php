<form action="{{route('payment.make')}}" method="POST">
    @csrf

    First Name
    <input type="text" name="first_name">

    Last Name
    <input type="text" name="last_name">

    Email
    <input type="email" name="email">

    Phone
    <input type="text" name="phone_number">

    Currency
    <select name="currency">
        <option value="KES">KES</option>
        <option value="UGX">UGX</option>
        <option value="TZS">TZS</option>
        <option value="USD">USD</option>
    </select>

    Amount
    <input type="text" name="amount">

    Description
    <input type="text" name="description" value="Payments to XYZ Company">

    <button type="submit">Make Payment</button>

</form>