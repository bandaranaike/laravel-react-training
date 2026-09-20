function DeleteConfirmModal({
                                employee,
                                onCancel,
                                onConfirm,
                            }) {
    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">

            <div className="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">

                <h2 className="text-xl font-bold">
                    Delete Employee
                </h2>

                <p className="mt-3 text-gray-600">
                    Are you sure you want to delete{" "}
                    <strong>{employee.name}</strong>?
                </p>

                <p className="mt-1 text-sm text-gray-500">
                    This action cannot be undone.
                </p>

                <div className="mt-6 flex justify-end gap-3">

                    <button
                        onClick={onCancel}
                        className="rounded-lg border px-4 py-2"
                    >
                        Cancel
                    </button>

                    <button
                        onClick={onConfirm}
                        className="rounded-lg bg-red-600 px-4 py-2 text-white"
                    >
                        Delete Employee
                    </button>

                </div>

            </div>

        </div>
    );
}

export default DeleteConfirmModal;