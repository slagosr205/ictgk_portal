import React, { useEffect, useState } from 'react';
import { useForm, Controller } from 'react-hook-form';
import {
    TextField,
    Button,
    MenuItem,
    Grid,
    Box,
    Typography,
    Select,
    FormControl,
    InputLabel
} from '@mui/material';
import axios from 'axios';
import { DatePicker } from '@mui/x-date-pickers';

const FormularioIngreso = ({ onSuccess, dataIngresoCandidato }) => {
    const { handleSubmit, control, reset, register, setValue } = useForm();
    const [dataUsuario, setDataUsuario] = useState([]);

    const onSubmit = async (data) => {
        try {
            console.log('Form data:', data);
            // await axios.post('/ingresos-nuevos', data, {
            //   headers: {
            //     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            //   },
            // });
            onSuccess?.();
            reset();
        } catch (error) {
            console.error('Error al registrar ingreso:', error);
        }
    };

    useEffect(() => {
        const fetchUserData = async () => {
            try {
                const response = await axios.get('/consulting-user');
                setDataUsuario(response.data.datosUsuarios || []);
            } catch (error) {
                console.error('Error al cargar datos de ingreso:', error);
            }
        };

        fetchUserData();
    }, []);



    const empresaNombre = dataUsuario[0]?.nombre || '';
    const empresaId = dataUsuario[0]?.empresa_id || '';

    useEffect(() => {
        if (empresaId) {
            setValue('empresa_id', empresaId);
        }
    }, [empresaId, setValue]);

    return (
        <Box component="form" onSubmit={handleSubmit(onSubmit)} sx={{ mt: 2 }} border={1} borderColor="grey.300" borderRadius={1} padding={2}>
            <Typography variant="h6" gutterBottom>
                Datos de Ingreso
            </Typography>

            <Grid container spacing={2}>
                {/* Identidad */}
                <Grid item xs={12} sm={6} size={4}>
                    <Controller
                        name="identidad"
                        control={control}
                        defaultValue={dataIngresoCandidato?.identidad || ''}
                        render={({ field }) => (
                            <TextField fullWidth label="Identidad" {...field} disabled required />
                        )}
                    />
                </Grid>
                {/* Campo oculto para empresa_id */}
                <Controller
                    name="empresa_id"
                    control={control}
                    defaultValue=""
                    render={({ field }) => (
                        <input type="hidden" {...field} />
                    )}
                />

                {/* Empresa (solo mostrar, no editar) */}
                <Grid item xs={12} sm={6} size={7}>
                    <TextField
                        fullWidth
                        label="Empresa"
                        value={empresaNombre}
                        disabled
                    />
                </Grid>

                {/* Área */}
                <Grid item xs={12} sm={6} size={4}>
                    <Controller
                        name="area"
                        control={control}
                        defaultValue=""
                        render={({ field }) => (
                            <TextField fullWidth label="Área" {...field} required />
                        )}
                    />
                </Grid>

                {/* Puesto */}
                <Grid item xs={12} sm={6} size={4}>
                    <FormControl fullWidth required>
                        <InputLabel id="puesto-label">Puesto</InputLabel>
                        <Controller
                            name="id_puesto"
                            control={control}
                            defaultValue=""
                            render={({ field }) => (
                                <Select labelId="puesto-label" label="Puesto" {...field}>
                                    {dataUsuario.map((puesto) => (
                                        <MenuItem key={puesto.id} value={puesto.id}>
                                            {puesto.nombrepuesto}
                                        </MenuItem>
                                    ))}
                                </Select>
                            )}
                        />
                    </FormControl>
                </Grid>

                {/* Fecha de ingreso */}
                <Grid item xs={12} sm={6} size={3}>
                    <Controller
                        name="fecha_ingreso"
                        control={control}
                        defaultValue={null} // usa `null` si es un DatePicker
                        rules={{ required: 'La fecha es requerida' }}
                        render={({ field, fieldState }) => (
                            <DatePicker
                                label="Fecha de Ingreso"
                                value={field.value}
                                onChange={(newValue) => field.onChange(newValue)}
                                renderInput={(params) => (
                                    <TextField
                                        {...params}
                                        fullWidth
                                        required
                                        error={!!fieldState.error}
                                        helperText={fieldState.error?.message}
                                    />
                                )}
                            />
                        )}
                    />
                </Grid>

                {/* Comentarios */}
                <Grid item xs={12} size={11}>
                    <Controller
                        name="comentarios"
                        control={control}
                        defaultValue=""
                        render={({ field }) => (
                            <TextField
                                fullWidth
                                label="Comentarios"
                                multiline
                                rows={3}
                                {...field}
                            />
                        )}
                    />
                </Grid>
            </Grid>

            {/* Botón de envío */}
            <Box sx={{ mt: 3 }}>
                <Button type="submit" variant="contained" color="primary">
                    Registrar Ingreso
                </Button>
            </Box>
        </Box>
    );
};

export default FormularioIngreso;
