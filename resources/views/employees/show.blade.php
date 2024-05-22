<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Employee Information
            </h2>

    </x-slot>

    <div class="flex mx-auto mt-8 space-x-3 max-w-7xl">

        <div class="w-1/4 p-5 bg-white rounded-md shadow ">
            <div class="mb-3 border-b border-gray-100">
                <h1 class="text-2xl font-bold text-center">Actions</h1>
            </div>
            <div class="flex flex-col space-y-2">
                <a href="{{ route('employees.edit', $employee) }}"
                    class="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">
                    Edit
                </a>

                <form class="flex flex-col" action="{{ route('employees.destroy', $employee) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900">
                        Delete
                    </button>
                </form>
                <hr>
                {{-- <a href="{{ route('seminars.payslip', ['employee_id' => $employee->id]) }}"
                    class="inline-flex items-center px-4 py-2 font-bold text-gray-800 bg-gray-300 rounded hover:bg-gray-400">
                    <svg class="w-4 h-4 mr-2 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z" />
                    </svg>
                    <span>Payslip (Seminars)</span>
                </a> --}}
                <a href="{{ route('employees.index') }}"
                    class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-gray-700 uppercase transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                    Back to Employee List
                </a>
            </div>
        </div>
        <div class="w-3/4 p-5 bg-white rounded-md shadow">
            <div class="mb-3 border-b border-gray-100">
                <h1 class="text-2xl font-bold">Personal Information</h1>
            </div>
            <div class="flex">
                <div class="mr-3">
                    <img src="{{ asset('storage/photos/' . $employee->employee_photo) }}" class="rounded"
                        style="height: 170px; width: 170px;">
                </div>
                <div>
                    <h3><strong>Employee No.: </strong>{{ $employee->employee_number }}</h3>
                    <h3><strong>Ordinance Item No.: </strong>{{ $employee->ordinance_number }}</h3>
                    <h3><strong>Name: </strong>{{ $employee->full_name }}</h3>

                    <h3><strong>Department: </strong>{{ $employee->data->department->dep_name }}</h3>
                    <h3><strong>Designation: </strong>{{ $employee->data->designation->designation_name }}</h3>
                    <h3><strong>Type of Employment: </strong>{{ $employee->data->category->category_name }}</h3>
                    @if ($employee->data->category->category_code == 'JO')
                        <h3><strong>Level: </strong> {{ $employee->data->level->name }}</h3>
                    @elseif ($employee->data->category->category_code != 'COS' && $employee->data->category->category_code != 'JO')
                        <h3><strong>Salary Grade: </strong> Salary Grade {{ $employee->data->salary_grade_id }}</h3>
                        <h3><strong>Salary Grade Step: </strong> {{ $employee->data->salary_grade_step }} </h3>
                    @endif
                    <h3><strong>Monthly Salary: </strong>
                        {{ number_format($employee->data->monthly_salary, 2) }}</h3>
                    @if ($employee->data->category->category_code != 'JO')
                        <h3><strong>Sick Leave Points:
                            </strong>{{ number_format($employee->data->sick_leave_points, 2) }}</h3>
                    @endif
                </div>
            </div>

            @if ($employee->data->category->category_code != 'JO')
                <div class="my-3 border-b border-gray-100">
                    <h1 class="text-2xl font-bold">Deductions & Allowances</h1>
                </div>
                @if (count($employee->allowances) > 0)
                    <div class="w-2/4">
                        <h3><strong>Allowances</strong></h3>
                        @php
                            $total_allowances = 0;
                        @endphp
                        @forelse ($allowances as $allowance)
                            @if ($employee->getAllowance($allowance->id) != 0)
                                @php
                                    $total_allowances =
                                        $total_allowances +
                                        $employee->getAllowance(
                                            $allowance->id,
                                        );
                                @endphp
                                <span>{{ $allowance->allowance_code }} -
                                    {{ number_format($employee->getAllowance($allowance->id), 2) }}
                                </span>
                                <br>
                            @else

                            @endif
                        @empty
                        @endforelse
                        <br>
                        <span>Total Allowance: {{ number_format($total_allowances) }}</span>
                    </div>
                @endif
                @if (count($employee->deductions) > 0)
                    <div class="w-2/4">
                        <h3><strong>Deductions</strong></h3>
                        @php
                            $total_deductions = $employee->computeDeduction();
                        @endphp
                        @forelse ($employee->deductions as $deduction)
                            <span>{{ $deduction->deduction->deduction_name }} -
                                {{ number_format($employee->getDeduction($deduction->deduction_id, null), 2) }}</span>
                            <br>
                        @empty
                            <span class="text-center">No Deductions</span>
                        @endforelse

                        @if ($employee->data->has_holding_tax)
                            @php
                                $total_deductions =
                                    $total_deductions +
                                    computeHoldingTax($employee->data->monthly_salary, $employee->computeDeduction());
                            @endphp
                            <span>With Holding Tax -
                                {{ number_format(computeHoldingTax($employee->data->monthly_salary, $employee->computeDeduction()), 2) }}</span>
                        @endif
                        <br>
                        <span>Total: {{ number_format($total_deductions, 2) }}</span>
                    </div>
                @endif
            @endif

            <div class="my-3 border-b border-gray-100">
                <h1 class="text-2xl font-bold">Other Information</h1>
            </div>
            @foreach ($employee->loans as $loan)
                @php
                    $balnce = 0;
                    $total_loan = 0;
                    $total_amount_paid = 0;
                    $loan_balance = 0;
                    $ranges = count($loan->ranges);
                    $duration =
                    $total_loan = $loan->amount * $loan->duration;
                @endphp
                <h2 class="mb-1"><strong>{{ $loan->loan->name }} - {{ number_format($total_loan, 2) }}</strong></h2>
                @foreach (getMonthsFromAttendance($employee) as $month)
                    @if (isBetweenDatesOfLoan($loan,$month->earliest_time_in))
                        @if ($total_amount_paid <= $total_loan)
                            @php
                                $total_amount_paid = $total_amount_paid + $loan->amount * $ranges;
                            @endphp
                            <h3>
                                <strong>{{ number_format($loan->amount * $ranges, 2) }}</strong>----------
                                {{ date('m', strtotime($month->earliest_time_in)) }}/{{ $ranges > 1 ? 30 : 15 }}/{{ date('Y', strtotime($month->earliest_time_in)) }}
                            </h3>
                        @endif
                    @endif
                @endforeach
                @php
                    $balance = $total_loan - $total_amount_paid;
                    if ($balance < 0) {
                        $balance = 0;
                    }
                @endphp
                <h3 class="mb-3"><strong>Balance: {{ number_format($balance, 2) }}</strong></h3>
            @endforeach
        </div>


    </div>
</x-app-layout>
