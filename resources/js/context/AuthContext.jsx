import React, { createContext, useState, useContext,useEffect  } from 'react';
import { LocalizationProvider } from '@mui/x-date-pickers';
import { AdapterDayjs } from '@mui/x-date-pickers/AdapterDayjs'
// Crear el contexto
const AuthContext = createContext();
//
// Proveedor del contexto
export const AuthProvider = ({ children }) => {
   /* const [isAuthenticated, setIsAuthenticated] = useState(() => {
        const storedAuth = localStorage.getItem('isAuthenticated');
        return storedAuth === 'true';
    });*/

 //   const [UrlSap, setUrlSap]=useState('');

   //variable global de uri API SAP
 /*   const url_service_sap =()=>{ 
        setUrlSap(import.meta.env.VITE_API_SAP);
        
    };*/

 /*   const login = () => {
        setIsAuthenticated(true);
        localStorage.setItem('isAuthenticated', 'true');
    };*/

 /*   useEffect(() => {
        url_service_sap(); // Llamar a la función para establecer la URL al cargar el componente
       
    }, []);*/

  /*  const logout = () => {
        setIsAuthenticated(false);
        localStorage.removeItem('isAuthenticated');
        localStorage.removeItem('selectedCompany');
        localStorage.removeItem("token_aprobador_ejecutivo");
        localStorage.removeItem('estado_aprobador_seleccionado');
        localStorage.removeItem('aprobadores');
        localStorage.removeItem('items');
    };*/

    
    return (
       <AuthContext.Provider > 
             <LocalizationProvider dateAdapter={AdapterDayjs}>
             {children}
             </LocalizationProvider>
           
       </AuthContext.Provider>
    );
};

// Hook para usar el contexto
export const useAuth = () => useContext(AuthContext);
//