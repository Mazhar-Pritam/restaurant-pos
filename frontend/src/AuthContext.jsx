import { createContext, useCallback, useContext, useEffect, useState } from 'react';
import api, { getCsrfCookie } from './api';

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  const loadUser = useCallback(async () => {
    try {
      const { data } = await api.get('/api/user');
      setUser(data);
    } catch {
      setUser(null);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    loadUser();
  }, [loadUser]);

  const login = async (email, password, remember) => {
    await getCsrfCookie();
    await api.post('/login', { email, password, remember });
    await loadUser();
  };

  const logout = async () => {
    await api.post('/logout');
    setUser(null);
  };

  // Only hides buttons. The server still checks every request.
  const can = (permission) => user?.permissions?.includes(permission) ?? false;

  return (
    <AuthContext.Provider value={{ user, loading, login, logout, can }}>
      {children}
    </AuthContext.Provider>
  );
}

// eslint-disable-next-line react-refresh/only-export-components
export const useAuth = () => useContext(AuthContext);
