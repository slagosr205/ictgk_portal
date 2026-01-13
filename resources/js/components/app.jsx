
import { Button } from "@mui/material";
import React from "react";
import ReactDOM from "react-dom/client";
import Busquedas from "./busquedas/Busquedas";
import { AuthProvider } from "../context/AuthContext";

const App = () => {
    return (
        <AuthProvider>
            <div>
                <Busquedas />
            
            </div>
        </AuthProvider>
        

    );
}

export default App;