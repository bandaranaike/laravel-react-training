import { useState } from "react";

const API_URL =
    "http://localhost:8000/api/employees";

function EmployeeModal({
                           employee,
                           onClose,
                           onSaved,
                       }) {
    const isEditing = Boolean(employee);

    const [form, setForm] = useState({
        name: employee?.name || "",
        email: employee?.email || "",
        department: employee?.department || "",
        position: employee?.position || "",
    });

    const [saving, setSaving] = useState(false);
    const [error, setError] = useState("");

    const handleChange = (event) => {
        setForm({
            ...form,
            [event.target.name]: event.target.value,
        });
    };

    const handleSubmit = async (event) => {
        event.preventDefault();

        setSaving(true);
        setError("");

        try {
            const url = isEditing
                ? `${API_URL}/${employee.id}`
                : API_URL;

            const response = await fetch(url, {
                method: isEditing ? "PUT" : "POST",

                headers: {
                    "Content-Type": "application/json",
                },

                body: JSON.stringify(form),
            });

            if (!response.ok) {
                throw new Error(
                    "Unable to save employee."
                );
            }

            await onSaved();

            onClose();

        } catch (error) {

            setError(error.message);

        } finally {

            setSaving(false);
        }
    };

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">

            <div className="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl">

                <h2 className="mb-6 text-xl font-bold">
                    {isEditing
                        ? "Edit Employee"
                        : "Add Employee"}
                </h2>

                {error && (
                    <div className="mb-4 rounded bg-red-100 p-3 text-red-700">
                        {error}
                    </div>
                )}

                <form
                    onSubmit={handleSubmit}
                    className="space-y-4"
                >

                    <div>
                        <label className="mb-1 block font-medium">
                            Name
                        </label>

                        <input
                            name="name"
                            value={form.name}
                            onChange={handleChange}
                            required
                            className="w-full rounded-lg border p-2"
                        />
                    </div>

                    <div>
                        <label className="mb-1 block font-medium">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            value={form.email}
                            onChange={handleChange}
                            required
                            className="w-full rounded-lg border p-2"
                        />
                    </div>

                    <div>
                        <label className="mb-1 block font-medium">
                            Department
                        </label>

                        <input
                            name="department"
                            value={form.department}
                            onChange={handleChange}
                            required
                            className="w-full rounded-lg border p-2"
                        />
                    </div>

                    <div>
                        <label className="mb-1 block font-medium">
                            Position
                        </label>

                        <input
                            name="position"
                            value={form.position}
                            onChange={handleChange}
                            required
                            className="w-full rounded-lg border p-2"
                        />
                    </div>

                    <div className="flex justify-end gap-3 pt-4">

                        <button
                            type="button"
                            onClick={onClose}
                            className="rounded-lg border px-4 py-2"
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            disabled={saving}
                            className="rounded-lg bg-blue-600 px-4 py-2 text-white disabled:opacity-50"
                        >
                            {saving
                                ? "Saving..."
                                : isEditing
                                    ? "Update Employee"
                                    : "Add Employee"}
                        </button>

                    </div>

                </form>

            </div>

        </div>
    );
}

export default EmployeeModal;