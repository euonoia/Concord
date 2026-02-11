<form action="{{ route('staff.login.submit') }}" method="POST">
    @csrf
    <div class="mb-4">
        <label class="block text-gray-700 font-bold mb-2">Employee ID Number</label>
        <input type="text" name="employee_code" placeholder="EMP-12345" 
               class="w-full border rounded p-2 focus:ring-2 focus:ring-red-500" required>
    </div>
    <div class="mb-6">
        <label class="block text-gray-700 font-bold mb-2">Security Password</label>
        <input type="password" name="password" 
               class="w-full border rounded p-2 focus:ring-2 focus:ring-red-500" required>
    </div>
    <button type="submit" class="w-full bg-red-600 text-white font-bold py-2 rounded hover:bg-red-700">
        Access HR System
    </button>
</form>