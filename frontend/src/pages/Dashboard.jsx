import { useAuth } from '../AuthContext';

const cards = [
  { permission: 'pos.use', label: 'POS and billing' },
  { permission: 'orders.create', label: 'Orders' },
  { permission: 'kitchen.view', label: 'Kitchen display' },
  { permission: 'inventory.manage', label: 'Inventory' },
  { permission: 'users.manage', label: 'Users' },
];

export default function Dashboard() {
  const { user, can } = useAuth();

  return (
    <>
      <h1 className="h4 mb-3">Welcome, {user.name}</h1>
      <div className="row g-3">
        {cards.filter((c) => can(c.permission)).map((c) => (
          <div className="col-md-3" key={c.permission}>
            <div className="card card-body">{c.label}</div>
          </div>
        ))}
      </div>
    </>
  );
}
