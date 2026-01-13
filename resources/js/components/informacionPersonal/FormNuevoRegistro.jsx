import React from "react";
import {
    Grid,
    TextField,
    FormControl,
    InputLabel,
    MenuItem,
    Select,
    Input,
} from "@mui/material";
import { blue, grey } from "@mui/material/colors";
import { useForm, Controller } from "react-hook-form";
import { DatePicker } from "@mui/x-date-pickers";
import axios from "axios";

const FormularioNuevoRegistro = ({
    setStatusNuevoRegistroCandidato,
    setNexStep,
    setDataNuevoIngreso,
    dniBuscado,
}) => {
    const {
        control,
        register,
        handleSubmit,
        formState: { errors },
    } = useForm();

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    const handleRegistroCandidato = async (data) => {
        try {
            const response = await axios.post("/ingresos-nuevos", data, {
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                },
            });
            setDataNuevoIngreso(data);
            setStatusNuevoRegistroCandidato(response.status);
        } catch (error) {
            console.error("Error submitting form:", error);
            setStatusNuevoRegistroCandidato(400);
        } finally {
            console.log("Registro de candidato finalizado");
        }
    };

    return (
        <Grid
            container
            mt={1}
            border={1}
            sx={{ borderColor: grey[300] }}
            borderRadius={1}
            padding={2}
        >
            <form onSubmit={handleSubmit(handleRegistroCandidato)}>
                <Grid container spacing={2} padding={2}>
                    <Grid item xs={12} sm={12}>
                        <TextField
                            fullWidth
                            label="Identidad"
                            {...register("identidad")}
                            defaultValue={dniBuscado}
                            required
                            disabled={dniBuscado !== ""}
                        />
                    </Grid>
                    <Grid item xs={12} sm={6}>
                        <TextField fullWidth label="Nombre" {...register("nombre")} required />
                    </Grid>
                    <Grid item xs={12} sm={6}>
                        <TextField fullWidth label="Apellido" {...register("apellido")} required />
                    </Grid>
                </Grid>

                <Grid container spacing={2} padding={2}>
                    <Grid item xs={12} sm={6}>
                        <TextField fullWidth label="Teléfono" {...register("telefono")} required />
                    </Grid>
                    <Grid item xs={12} sm={6}>
                        <TextField fullWidth label="Correo" {...register("correo")} required />
                    </Grid>

                    <Grid item xs={6} size={4}>
                        <FormControl fullWidth>
                            <InputLabel>Género</InputLabel>
                            <Controller
                                name="generoM_F"
                                control={control}
                                defaultValue=""
                                render={({ field }) => (
                                    <Select {...field} label="Género">
                                        <MenuItem value="m">Masculino</MenuItem>
                                        <MenuItem value="f">Femenino</MenuItem>
                                    </Select>
                                )}
                            />
                        </FormControl>
                    </Grid>
                </Grid>

                <Grid container spacing={2} padding={2}>
                    <Grid item xs={12}>
                        <TextField fullWidth label="Dirección" {...register("direccion")} required />
                    </Grid>
                    <Grid item xs={6} size={4}>
                        <FormControl fullWidth>
                            <InputLabel>Activo</InputLabel>
                            <Controller
                                name="activo"
                                control={control}
                                defaultValue=""
                                render={({ field }) => (
                                    <Select {...field} label="Activo">
                                        <MenuItem value="s">Sí</MenuItem>
                                        <MenuItem value="n">No</MenuItem>
                                    </Select>
                                )}
                            />
                        </FormControl>
                    </Grid>
                </Grid>

                <Grid container spacing={2} padding={2}>
                    <Grid item xs={12} sm={6}>
                        <Controller
                            name="fecha_nacimiento"
                            control={control}
                            defaultValue={null}
                            render={({ field }) => (
                                <DatePicker
                                    label="Fecha de nacimiento"
                                    value={field.value ? dayjs(field.value) : null}
                                    onChange={(date) => {
                                        const formattedDate = date ? dayjs(date).format('YYYY-MM-DD') : '';
                                        field.onChange(formattedDate);
                                    }}
                                    slotProps={{ textField: { fullWidth: true, required: true } }}
                                />
                            )}
                        />
                    </Grid>
                </Grid>

                <Grid container spacing={2} padding={2}>
                    <Grid item xs={12}>
                        <TextField
                            fullWidth
                            multiline
                            rows={3}
                            label="Comentarios"
                            {...register("comentarios")}
                            required
                        />
                    </Grid>
                </Grid>

                <Grid item xs={12} padding={2}>
                    <Input
                        type="submit"
                        value="Registrar Candidato"
                        fullWidth
                        sx={{ bgcolor: blue[200], border: "1px", borderRadius: "3px" }}
                    />
                </Grid>
            </form>
        </Grid>
    );
};

export default FormularioNuevoRegistro;
