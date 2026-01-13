import React, { useState } from 'react';
import {
    Grid,
    Typography,
    Avatar,
    Paper,
    Chip,
    Divider,
    Box,
    TextField,
    Button
} from '@mui/material';
import { BlockSharp, BubbleChart, CheckCircle, Update } from '@mui/icons-material';
import { green, grey, red } from '@mui/material/colors';
import ModalCandidato from './StepperNuevoRegistro';

const PerfilCandidato = ({ dataCandidatos }) => {
    

    if (!dataCandidatos){
        return null;
    } 



    const {
        identidad,
        nombre,
        apellido,
        telefono,
        correo,
        direccion,
        generoM_F,
        activo,
        fecha_nacimiento,
        created_at,
        updated_at,
    } = dataCandidatos;

    const nombreCompleto = `${nombre} ${apellido}`;

    return (
        <Paper elevation={3} sx={{ p: 3, borderRadius: 3 }} >
            <Grid container spacing={4}>
                {/* Columna izquierda: Perfil */}
                <Grid item xs={12} md={4}>
                    <Box display="flex" flexDirection="column" alignItems="center" textAlign="center">
                        <Avatar
                            sx={{ width: 100, height: 100, bgcolor: 'primary.main', fontSize: 36 }}
                        >
                            {nombre[0]}{apellido[0]}
                        </Avatar>
                        <Typography variant="h6" fontWeight="bold" mt={2}>
                            {nombreCompleto}
                        </Typography>
                        <Typography variant="body2" color="text.secondary">
                            Identidad: {identidad}
                        </Typography>
                        <Chip
                            label={activo === 's' ? 'Activo' : 'Inactivo'}
                         
                            sx={{ mt: 1 , bgcolor: activo === 's' ? red[400]:green[400]  , color: activo === 's' ? grey[800] : grey[800] }}
                            icon={activo === 's' ? <CheckCircle /> : <BlockSharp />}
                        />
                    </Box>
                </Grid>

                {/* Columna derecha: Información personal */}
                <Grid item xs={8} md={8}>
                    <Typography variant="h6" gutterBottom>
                        Información Personal
                    </Typography>
                    <Divider sx={{ mb: 2 }} />

                    <Grid container spacing={2}>
                        <Grid item xs={12} sm={6}>
                            <TextField
                                label="Teléfono"
                                value={telefono}
                                fullWidth
                                InputProps={{ readOnly: true }}
                            />
                        </Grid>
                        <Grid item xs={12} sm={6}>
                            <TextField
                                label="Correo"
                                value={correo}
                                fullWidth
                                InputProps={{ readOnly: true }}
                            />
                        </Grid>
                        <Grid item xs={12}>
                            <TextField
                                label="Dirección"
                                value={direccion}
                                fullWidth
                                InputProps={{ readOnly: true }}
                            />
                        </Grid>
                    </Grid>
                    <Grid container spacing={2} paddingTop={3}>
                        <Grid item xs={12} sm={6}>
                            <TextField
                                label="Fecha de nacimiento"
                                value={fecha_nacimiento}
                                fullWidth
                                InputProps={{ readOnly: true }}
                            />
                        </Grid>
                        <Grid item xs={12} sm={6}>
                            <TextField
                                label="Creado"
                                value={created_at}
                                fullWidth
                                InputProps={{ readOnly: true }}
                            />
                        </Grid>
                        <Grid item xs={12}>
                            <TextField
                                label="Actualizado"
                                value={updated_at}
                                fullWidth
                                InputProps={{ readOnly: true }}
                            />
                        </Grid>
                    </Grid>
                </Grid>
            </Grid>
              <Grid container spacing={2} paddingTop={3} justifyContent="right">
                <Grid item xs={12}>
                   <Button variant='contained'><Update></Update> Actualizar </Button>
                </Grid>
              </Grid>
                
        </Paper >
    );
}

export default PerfilCandidato;
