import { useEffect, useState } from "react";
import EmployeeTable from "./components/EmployeeTable";
import EmployeeModal from "./components/EmployeeModal";
import DeleteConfirmModal from "./components/DeleteConfirmModal";

const API_URL =
    "http://localhost:8000/api/employees";

function App() {
  const [employees, setEmployees] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  const [showModal, setShowModal] = useState(false);
  const [selectedEmployee, setSelectedEmployee] =
      useState(null);

  const [employeeToDelete, setEmployeeToDelete] =
      useState(null);

  const fetchEmployees = async () => {
    setLoading(true);
    setError("");

    try {
      const response = await fetch(API_URL);

      if (!response.ok) {
        throw new Error("Unable to load employees.");
      }

      const data = await response.json();

      setEmployees(data);
    } catch (error) {
      setError(error.message);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchEmployees();
  }, []);

  const handleAdd = () => {
    setSelectedEmployee(null);
    setShowModal(true);
  };

  const handleEdit = (employee) => {
    setSelectedEmployee(employee);
    setShowModal(true);
  };

  const handleCloseModal = () => {
    setShowModal(false);
    setSelectedEmployee(null);
  };

  const handleDelete = async () => {
    try {
      const response = await fetch(
          `${API_URL}/${employeeToDelete.id}`,
          {
            method: "DELETE",
          }
      );

      if (!response.ok) {
        throw new Error("Unable to delete employee.");
      }

      setEmployeeToDelete(null);

      await fetchEmployees();
    } catch (error) {
      setError(error.message);
    }
  };

  return (
      <div className="min-h-screen bg-gray-100">

        <div className="mx-auto max-w-7xl p-8">

          <div className="mb-6 flex items-center justify-between">

            <div>
              <h1 className="text-3xl font-bold text-gray-900">
                Employees
              </h1>

              <p className="mt-1 text-gray-500">
                Manage company employees
              </p>
            </div>

            <button
                onClick={handleAdd}
                className="rounded-lg bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700"
            >
              + Add Employee
            </button>

          </div>

          {error && (
              <div className="mb-4 rounded-lg bg-red-100 p-4 text-red-700">
                {error}
              </div>
          )}

          {loading ? (
              <div className="p-10 text-center text-gray-500">
                Loading employees...
              </div>
          ) : (
              <EmployeeTable
                  employees={employees}
                  onEdit={handleEdit}
                  onDelete={setEmployeeToDelete}
              />
          )}

        </div>

        {showModal && (
            <EmployeeModal
                employee={selectedEmployee}
                onClose={handleCloseModal}
                onSaved={fetchEmployees}
            />
        )}

        {employeeToDelete && (
            <DeleteConfirmModal
                employee={employeeToDelete}
                onCancel={() =>
                    setEmployeeToDelete(null)
                }
                onConfirm={handleDelete}
            />
        )}

      </div>
  );
}

export default App;