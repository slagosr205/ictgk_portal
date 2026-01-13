// ModalCandidato.js
import React, { useState } from 'react';
import {
  Dialog, DialogTitle, DialogContent, DialogActions,
  TextField, Button, Grid, FormControl, InputLabel, Select, MenuItem,
  Stepper,
  Step,
  StepLabel,
  Typography
} from '@mui/material';
import { useForm } from 'react-hook-form';
import { data } from 'jquery';
import FormularioNuevoRegistro from './FormNuevoRegistro';
import FormularioIngreso from './FormularioIngreso';
import { green } from '@mui/material/colors';

const ModalCandidato = ({ open, onClose, candidato, dniBuscado }) => {
  const [statusNuevoRegistroCandidato,setStatusNuevoRegistroCandidato] = useState(false);
  const [canNextStep, setCanNextStep] = useState(false);
  const [activeStep, setActiveStep] = useState(0);
  const [skipped, setSkipped] = useState(new Set());
  const [dataNuevoIngreso, setDataNuevoIngreso] = useState(null);
  const steps = ['Información Personal', 'Información Laboral', 'Proceso Terminado'];

  const isStepOptional = (step) => {
    return step === 1;
  };

  const isStepSkipped = (step) => {
    return skipped.has(step);
  };

  const handleNext = () => {
    let newSkipped = skipped;
    if (isStepSkipped(activeStep)) {
      newSkipped = new Set(newSkipped.values());
      newSkipped.delete(activeStep);
    }

    setActiveStep((prevActiveStep) => prevActiveStep + 1);
    setSkipped(newSkipped);
  };

  const handleBack = () => {
    setActiveStep((prevActiveStep) => prevActiveStep - 1);
  };

  const handleSkip = () => {
    if (!isStepOptional(activeStep)) {
      // You probably want to guard against something like this,
      // it should never occur unless someone's actively trying to break something.
      throw new Error("You can't skip a step that isn't optional.");
    }

    setActiveStep((prevActiveStep) => prevActiveStep + 1);
    setSkipped((prevSkipped) => {
      const newSkipped = new Set(prevSkipped.values());
      newSkipped.add(activeStep);
      return newSkipped;
    });
  };

  const handleReset = () => {
    setActiveStep(0);
  };

  // Componentes por paso
  const renderStepContent = (step) => {
    switch (step) {
      case 0:
        return <FormularioNuevoRegistro setStatusNuevoRegistroCandidato={setStatusNuevoRegistroCandidato} setDataNuevoIngreso={setDataNuevoIngreso} dniBuscado={dniBuscado} />;
      case 1:
        return <FormularioIngreso onSuccess={handleNext} dataIngresoCandidato={dataNuevoIngreso}/>;

      case 2:
        return <Typography variant="h6" color="primary">✅ Proceso completado con éxito.</Typography>;
      default:
        return null;
    }
  };

  return (
     <Dialog open={open} onClose={onClose} maxWidth="md" fullWidth>
       {console.log("DNI Buscado:", dniBuscado)}
      <DialogTitle>Registrar Nuevo Candidato</DialogTitle>
      <DialogContent>
        <Stepper activeStep={activeStep} sx={{ mb: 2, bgcolor:green[50], borderRadius: 1 }} >
          {steps.map((label, index) => {
            const stepProps = {};
            const labelProps = {};
            if (isStepOptional(index)) {
              labelProps.optional = (
                <Typography variant="caption">Opcional</Typography>
              );
            }
            if (isStepSkipped(index)) {
              stepProps.completed = false;
            }
            return (
              <Step key={label} {...stepProps}>
                <StepLabel {...labelProps}>{label}</StepLabel>
              </Step>
            );
          })}
        </Stepper>

        {renderStepContent(activeStep)}
      </DialogContent>

      <DialogActions>
        <Button onClick={onClose}>Cancelar</Button>
        {activeStep !== 0 && (
          <Button onClick={handleBack}>Atrás</Button>
        )}
        {activeStep < steps.length - 1 ? (
          <Button onClick={handleNext} variant="contained" disabled={statusNuevoRegistroCandidato===200}>Siguiente</Button>
        ) : (
          <Button onClick={handleReset} variant="outlined">Finalizar</Button>
        )}
      </DialogActions>
    </Dialog>
  );
}

export default ModalCandidato;
