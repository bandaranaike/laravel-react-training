function EmployeeTable({
                           employees,
                           onEdit,
                           onDelete,
                       }) {
    if (employees.length === 0) {
        return (
            <div className="rounded-xl bg-white p-10 text-center shadow-sm">
                <p className="text-gray-500">
                    No employees found.
                </p>
            </div>
        );
    }

    return (
        <div className="overflow-hidden rounded-xl bg-white shadow-sm">

            <table className="w-full">

                <thead className="bg-gray-50">
                <tr>
                    <th className="p-4 text-left">Name</th>
                    <th className="p-4 text-left">Email</th>
                    <th className="p-4 text-left">Department</th>
                    <th className="p-4 text-left">Position</th>
                    <th className="p-4 text-right">Actions</th>
                </tr>
                </thead>

                <tbody className="divide-y divide-gray-200">

                {employees.map((employee) => (
                    <tr
                        key={employee.id}
                        className="hover:bg-gray-50"
                    >
                        <td className="p-4">
                            {employee.name}
                        </td>

                        <td className="p-4">
                            {employee.email}
                        </td>

                        <td className="p-4">
                            {employee.department}
                        </td>

                        <td className="p-4">
                            {employee.position}
                        </td>

                        <td className="p-4 text-right">

                            <button
                                onClick={() => onEdit(employee)}
                                className="mr-3 text-blue-600 hover:underline"
                            >
                                Edit
                            </button>

                            <button
                                onClick={() => onDelete(employee)}
                                className="text-red-600 hover:underline"
                            >
                                Delete
                            </button>

                        </td>
                    </tr>
                ))}

                </tbody>

            </table>

        </div>
    );
}

export default EmployeeTable;